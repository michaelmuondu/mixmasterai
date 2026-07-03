<?php

// ===============================
// Helper Functions for MixMaster AI
// ===============================

/**
 * Format time in seconds to MM:SS format
 */
function formatTime($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%02d:%02d", $minutes, $secs);
}

/**
 * Format file size to human readable format
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    $filename = basename($filename);
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return $filename;
}

/**
 * Check if user owns a song
 */
function userOwnsSong($pdo, $userId, $songId) {
    $stmt = $pdo->prepare("SELECT id FROM songs WHERE id = ? AND uploaded_by = ?");
    $stmt->execute([$songId, $userId]);
    return $stmt->rowCount() > 0;
}

/**
 * Check if user owns a playlist
 */
function userOwnsPlaylist($pdo, $userId, $playlistId) {
    $stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$playlistId, $userId]);
    return $stmt->rowCount() > 0;
}

/**
 * Get user's listening statistics
 */
function getListeningStats($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT song_id) as total_unique,
            COUNT(*) as total_plays,
            MAX(played_at) as last_played
        FROM play_history
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Get top genres for user
 */
function getTopGenres($pdo, $userId, $limit = 5) {
    $stmt = $pdo->prepare("
        SELECT 
            s.genre,
            COUNT(*) as play_count
        FROM play_history ph
        JOIN songs s ON ph.song_id = s.id
        WHERE ph.user_id = ? AND s.genre IS NOT NULL
        GROUP BY s.genre
        ORDER BY play_count DESC
        LIMIT ?
    ");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get user's favorite artists
 */
function getFavoriteArtists($pdo, $userId, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT 
            s.artist,
            COUNT(*) as play_count
        FROM play_history ph
        JOIN songs s ON ph.song_id = s.id
        WHERE ph.user_id = ? AND s.artist IS NOT NULL
        GROUP BY s.artist
        ORDER BY play_count DESC
        LIMIT ?
    ");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Log song play
 */
function logSongPlay($pdo, $userId, $songId) {
    $stmt = $pdo->prepare("
        INSERT INTO play_history (user_id, song_id)
        VALUES (?, ?)
    ");
    return $stmt->execute([$userId, $songId]);
}

/**
 * Validate audio file
 */
function validateAudioFile($filePath) {
    if (!file_exists($filePath)) {
        return ['valid' => false, 'error' => 'File not found'];
    }
    
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $allowed = ['mp3', 'wav', 'ogg', 'flac'];
    
    if (!in_array($ext, $allowed)) {
        return ['valid' => false, 'error' => 'Unsupported audio format'];
    }
    
    return ['valid' => true];
}

/**
 * Ensure uploads directory exists
 */
function ensureUploadsDirectory() {
    $uploadDir = __DIR__ . '/../uploads/songs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    return $uploadDir;
}
