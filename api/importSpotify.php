<?php
session_start();
header('Content-Type: application/json');

require_once("../includes/db.php");
require_once("../includes/config.php");
require_once("../includes/functions.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized.'
    ]);
    exit();
}

$spotifyUrl = trim($_POST['spotify_url'] ?? '');

if (!$spotifyUrl) {
    echo json_encode([
        'success' => false,
        'message' => 'Spotify URL is required.'
    ]);
    exit();
}

$clientId = SPOTIFY_CLIENT_ID;
$clientSecret = SPOTIFY_CLIENT_SECRET;

if (!$clientId || !$clientSecret || $clientId === 'your_spotify_client_id') {
    echo json_encode([
        'success' => false,
        'message' => 'Spotify credentials are not configured.'
    ]);
    exit();
}

function getSpotifyAccessToken($clientId, $clientSecret) {
    $tokenUrl = 'https://accounts.spotify.com/api/token';
    $body = http_build_query(['grant_type' => 'client_credentials']);

    $headers = [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
        'Content-Type: application/x-www-form-urlencoded'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'message' => $error];
    }

    $data = json_decode($response, true);
    if ($status !== 200 || !isset($data['access_token'])) {
        $message = 'Unable to retrieve Spotify access token.';
        if (isset($data['error_description'])) {
            $message = $data['error_description'];
        } elseif (isset($data['error'])) {
            $message = is_array($data['error']) ? json_encode($data['error']) : $data['error'];
        }
        return ['success' => false, 'message' => $message];
    }

    return ['success' => true, 'token' => $data['access_token']];
}

function parseSpotifyId($url) {
    if (preg_match('#spotify\.com/(album|playlist)/([A-Za-z0-9]+)#', $url, $matches)) {
        return ['type' => $matches[1], 'id' => $matches[2]];
    }
    if (preg_match('#spotify:(album|playlist):([A-Za-z0-9]+)#', $url, $matches)) {
        return ['type' => $matches[1], 'id' => $matches[2]];
    }
    return null;
}

$parsed = parseSpotifyId($spotifyUrl);
if (!$parsed) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Spotify album or playlist URL.'
    ]);
    exit();
}

$tokenResult = getSpotifyAccessToken($clientId, $clientSecret);
if (!$tokenResult['success']) {
    echo json_encode([
        'success' => false,
        'message' => $tokenResult['message']
    ]);
    exit();
}

$accessToken = $tokenResult['token'];
$endpoint = "https://api.spotify.com/v1/{$parsed['type']}s/{$parsed['id']}";

$ch = curl_init($endpoint . '?market=US');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => $error
    ]);
    exit();
}

$data = json_decode($response, true);
if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Spotify response.'
    ]);
    exit();
}

if ($status >= 400) {
    $message = 'Spotify API request failed.';
    if (isset($data['error'])) {
        $message = is_array($data['error']) ? ($data['error']['message'] ?? json_encode($data['error'])) : $data['error'];
    }
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit();
}

$tracks = [];
$albumName = '';
$artistNames = [];
$coverImage = null;

if ($parsed['type'] === 'album') {
    $albumName = $data['name'] ?? '';
    $artistNames = array_map(fn($artist) => $artist['name'], $data['artists'] ?? []);
    $coverImage = $data['images'][0]['url'] ?? null;
    $tracks = $data['tracks']['items'] ?? [];
} else {
    $albumName = $data['name'] ?? '';
    $artistNames = [];
    $coverImage = $data['images'][0]['url'] ?? null;
    $tracks = $data['tracks']['items'] ?? [];
}

if (!$tracks) {
    echo json_encode([
        'success' => false,
        'message' => 'No tracks found in the selected Spotify resource.'
    ]);
    exit();
}

$imported = 0;

foreach ($tracks as $item) {
    $track = $item['track'] ?? $item;
    $title = $track['name'] ?? 'Unknown Title';
    $artist = $track['artists'][0]['name'] ?? 'Unknown Artist';
    $album = $track['album']['name'] ?? $albumName;
    $genre = '';
    $duration = isset($track['duration_ms']) ? gmdate('H:i:s', (int)$track['duration_ms'] / 1000) : null;
    $previewUrl = $track['preview_url'] ?? null;
    $trackCover = $coverImage ?? ($track['album']['images'][0]['url'] ?? null);

    if (!$previewUrl) {
        continue;
    }

    $stmt = $pdo->prepare("SELECT id FROM songs WHERE file_path = ? AND uploaded_by = ?");
    $stmt->execute([$previewUrl, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        continue;
    }

    $insert = $pdo->prepare("INSERT INTO songs (title, artist, album, genre, duration, file_path, cover_image, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([
        $title,
        $artist,
        $album,
        $genre,
        $duration,
        $previewUrl,
        $trackCover,
        $_SESSION['user_id']
    ]);

    $imported++;
}

if ($imported === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'No Spotify preview tracks were available to import.'
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'message' => "Imported {$imported} preview tracks from Spotify.",
    'imported' => $imported
]);
