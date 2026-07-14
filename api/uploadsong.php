<?php

session_start();

header('Content-Type: application/json');

require_once("../includes/db.php");
require_once("../includes/functions.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit();
}

if (!isset($_FILES['song'])) {
    echo json_encode([
        "success" => false,
        "message" => "No file selected."
    ]);
    exit();
}

$file = $_FILES['song'];

$allowed = ['mp3', 'wav'];

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $allowed)) {
    echo json_encode([
        "success" => false,
        "message" => "Only MP3 and WAV files are allowed."
    ]);
    exit();
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "success" => false,
        "message" => "Upload failed."
    ]);
    exit();
}

$title = trim($_POST['title'] ?? '');
$artist = trim($_POST['artist'] ?? '');
$album = trim($_POST['album'] ?? '');
$genre = trim($_POST['genre'] ?? '');

if (empty($title)) {
    echo json_encode([
        "success" => false,
        "message" => "Title is required."
    ]);
    exit();
}

// Ensure uploads directory exists
$uploadDir = ensureUploadsDirectory();

$newName = bin2hex(random_bytes(16)) . "." . $extension;

$destination = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {

    echo json_encode([
        "success" => false,
        "message" => "Upload failed.",
        "destination" => $destination,
        "error" => error_get_last()
    ]);

    exit();
}

$filePath = "../uploads/songs/" . $newName;

// Insert song into database
$stmt = $pdo->prepare("
INSERT INTO songs
(title, artist, album, genre, file_path, cover_image, uploaded_by)
VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $title,
    $artist,
    $album,
    $genre,
    $filePath,
    null,
    $_SESSION['user_id']
]);

$songId = $pdo->lastInsertId();

// Run AI detection asynchronously (optional)
// You could trigger these via separate API calls or a queue system
$detectionData = [
    'song_id' => $songId,
    'title' => $title,
    'artist' => $artist,
    'genre_initial' => $genre
];

echo json_encode([
    "success" => true,
    "message" => "Song uploaded successfully!",
    "song_id" => $songId,
    "filename" => $newName
]);