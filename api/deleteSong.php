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
SELECT filename
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

$file = "../uploads/songs/" . $song['filename'];

if (file_exists($file)) {
    unlink($file);
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