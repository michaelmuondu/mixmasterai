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

// Total Playlists (0 for now)
$totalPlaylists = 0;

// Total AI Mixes (0 for now)
$totalMixes = 0;

// Storage Used
$stmt = $pdo->prepare("SELECT SUM(file_size) FROM songs WHERE uploaded_by = ?");
$stmt->execute([$userId]);
$storage = $stmt->fetchColumn();

if (!$storage) {
    $storage = 0;
}

echo json_encode([
    "success" => true,
    "songs" => $totalSongs,
    "playlists" => $totalPlaylists,
    "mixes" => $totalMixes,
    "storage" => round($storage / 1024 / 1024, 2)
]);