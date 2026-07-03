<?php

session_start();

header('Content-Type: application/json');

require_once("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false
    ]);
    exit();
}

$userId = $_SESSION['user_id'];

// Total Songs
$stmt = $pdo->prepare("SELECT COUNT(*) FROM songs WHERE uploaded_by = ?");
$stmt->execute([$userId]);
$totalSongs = $stmt->fetchColumn();

// Total Playlists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM playlists WHERE user_id = ?");
$stmt->execute([$userId]);
$totalPlaylists = $stmt->fetchColumn();

// Total Play History (tracks plays)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM play_history WHERE user_id = ?");
$stmt->execute([$userId]);
$totalPlays = $stmt->fetchColumn();

// Storage Used
$stmt = $pdo->prepare("SELECT SUM(file_size) FROM songs WHERE uploaded_by = ?");
$stmt->execute([$userId]);
$storage = $stmt->fetchColumn();

if (!$storage) {
    $storage = 0;
}

// Get listening stats
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT song_id) as unique_songs, MAX(played_at) as last_played
    FROM play_history
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

echo json_encode([
    "success" => true,
    "songs" => $totalSongs,
    "playlists" => $totalPlaylists,
    "plays" => $totalPlays,
    "unique_played" => $stats['unique_songs'] ?? 0,
    "storage" => round($storage / 1024 / 1024, 2),
    "last_played" => $stats['last_played'] ?? null
]);