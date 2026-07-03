<?php

session_start();

header('Content-Type: application/json');

require_once("../includes/db.php");

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

$title = trim($_POST['title']);
$artist = trim($_POST['artist']);
$genre = trim($_POST['genre']);

$newName = bin2hex(random_bytes(16)) . "." . $extension;

$destination = "../uploads/songs/" . $newName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {

    echo json_encode([
        "success" => false,
        "message" => "Upload failed.",
        "tmp_name" => $file['tmp_name'],
        "destination" => $destination,
        "error" => error_get_last()
    ]);

    exit();
}

$stmt = $pdo->prepare("
INSERT INTO songs
(title, artist, genre, filename, file_size, uploaded_by)
VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $title,
    $artist,
    $genre,
    $newName,
    $file['size'],
    $_SESSION['user_id']
]);

echo json_encode([
    "success" => true,
    "message" => "Song uploaded successfully!"
]);