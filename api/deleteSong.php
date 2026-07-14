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

if (!isset($_POST['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Song ID missing."
    ]);
    exit();
}

$id = (int)$_POST['id'];

$stmt = $pdo->prepare("
SELECT file_path
FROM songs
WHERE id = ?
AND uploaded_by = ?
");

$stmt->execute([$id, $_SESSION['user_id']]);

$song = $stmt->fetch();

if (!$song) {
    echo json_encode([
        "success" => false,
        "message" => "Song not found."
    ]);
    exit();
}

$filePath = $song['file_path'];

if ($filePath && !preg_match('#^https?://#i', $filePath)) {
    $localFile = realpath(dirname(__DIR__) . '/' . ltrim($filePath, './'));
    $uploadsDir = realpath(__DIR__ . '/../uploads/songs/');

    if ($localFile && strpos($localFile, $uploadsDir) === 0 && file_exists($localFile)) {
        unlink($localFile);
    }
}

$stmt = $pdo->prepare("
DELETE FROM songs
WHERE id = ?
AND uploaded_by = ?
");

$stmt->execute([$id, $_SESSION['user_id']]);

echo json_encode([
    "success" => true,
    "message" => "Song deleted successfully."
]);