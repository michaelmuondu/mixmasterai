<?php

// ===============================
// Save/Create Playlist API
// ===============================

session_start();
header('Content-Type: application/json');

require_once("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? null;
    
    if ($action === 'create') {
        // Create new playlist
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name)) {
            echo json_encode([
                'success' => false,
                'message' => 'Playlist name required'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO playlists (name, description, user_id)
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$name, $description, $userId])) {
            $playlistId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Playlist created',
                'playlist_id' => $playlistId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create playlist'
            ]);
        }
        
    } elseif ($action === 'add_song') {
        // Add song to playlist
        $playlistId = $_POST['playlist_id'] ?? null;
        $songId = $_POST['song_id'] ?? null;
        
        if (!$playlistId || !$songId) {
            echo json_encode([
                'success' => false,
                'message' => 'Playlist ID and Song ID required'
            ]);
            exit;
        }
        
        // Verify user owns playlist
        $stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ?");
        $stmt->execute([$playlistId, $userId]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            exit;
        }
        
        // Get next position
        $stmt = $pdo->prepare("
            SELECT MAX(position) as max_pos FROM playlist_songs
            WHERE playlist_id = ?
        ");
        $stmt->execute([$playlistId]);
        $result = $stmt->fetch();
        $position = ($result['max_pos'] ?? 0) + 1;
        
        // Add song
        $stmt = $pdo->prepare("
            INSERT INTO playlist_songs (playlist_id, song_id, position)
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$playlistId, $songId, $position])) {
            echo json_encode([
                'success' => true,
                'message' => 'Song added to playlist'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add song'
            ]);
        }
        
    } elseif ($action === 'remove_song') {
        // Remove song from playlist
        $playlistId = $_POST['playlist_id'] ?? null;
        $songId = $_POST['song_id'] ?? null;
        
        if (!$playlistId || !$songId) {
            echo json_encode([
                'success' => false,
                'message' => 'Playlist ID and Song ID required'
            ]);
            exit;
        }
        
        // Verify user owns playlist
        $stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ?");
        $stmt->execute([$playlistId, $userId]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            DELETE FROM playlist_songs
            WHERE playlist_id = ? AND song_id = ?
        ");
        
        if ($stmt->execute([$playlistId, $songId])) {
            echo json_encode([
                'success' => true,
                'message' => 'Song removed from playlist'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to remove song'
            ]);
        }
        
    } elseif ($action === 'update') {
        // Update playlist
        $playlistId = $_POST['playlist_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (!$playlistId) {
            echo json_encode([
                'success' => false,
                'message' => 'Playlist ID required'
            ]);
            exit;
        }
        
        // Verify user owns playlist
        $stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ?");
        $stmt->execute([$playlistId, $userId]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            UPDATE playlists
            SET name = ?, description = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$name, $description, $playlistId])) {
            echo json_encode([
                'success' => true,
                'message' => 'Playlist updated'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update playlist'
            ]);
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user's playlists
    $stmt = $pdo->prepare("
        SELECT p.*, COUNT(ps.song_id) as song_count
        FROM playlists p
        LEFT JOIN playlist_songs ps ON p.id = ps.playlist_id
        WHERE p.user_id = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$userId]);
    $playlists = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'playlists' => $playlists
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
