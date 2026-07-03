<?php

// ===============================
// NextSong API - Get next song in playlist
// ===============================

session_start();
header('Content-Type: application/json');

require_once("../includes/db.php");
require_once("../includes/functions.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$playlistId = $_GET['playlist_id'] ?? null;
$currentSongId = $_GET['current_song_id'] ?? null;
$shuffle = $_GET['shuffle'] ?? false;

if (!$playlistId) {
    echo json_encode(['error' => 'Playlist ID required']);
    exit;
}

// Verify user owns playlist
$stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlistId, $_SESSION['user_id']]);

if (!$stmt->fetch()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($shuffle) {
    // Get random song from playlist
    $stmt = $pdo->prepare("
        SELECT s.* FROM songs s
        JOIN playlist_songs ps ON s.id = ps.song_id
        WHERE ps.playlist_id = ?
        ORDER BY RAND()
        LIMIT 1
    ");
    $stmt->execute([$playlistId]);
} else {
    // Get next song in sequence
    if ($currentSongId) {
        $stmt = $pdo->prepare("
            SELECT s.* FROM songs s
            JOIN playlist_songs ps ON s.id = ps.song_id
            WHERE ps.playlist_id = ?
            AND ps.position > (
                SELECT position FROM playlist_songs 
                WHERE song_id = ? AND playlist_id = ?
            )
            ORDER BY ps.position ASC
            LIMIT 1
        ");
        $stmt->execute([$playlistId, $currentSongId, $playlistId]);
    } else {
        // Get first song
        $stmt = $pdo->prepare("
            SELECT s.* FROM songs s
            JOIN playlist_songs ps ON s.id = ps.song_id
            WHERE ps.playlist_id = ?
            ORDER BY ps.position ASC
            LIMIT 1
        ");
        $stmt->execute([$playlistId]);
    }
}

$song = $stmt->fetch();

if ($song) {
    // Log play
    logSongPlay($pdo, $_SESSION['user_id'], $song['id']);
    
    echo json_encode([
        'success' => true,
        'song' => $song
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No more songs in playlist'
    ]);
}
