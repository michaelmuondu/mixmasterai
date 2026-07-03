<?php

// ===============================
// Auto DJ API - AI-powered DJ mixing
// ===============================

session_start();
header('Content-Type: application/json');

require_once("../includes/db.php");
require_once("../ai/recommendationEngine.php");
require_once("../ai/keyDetector.php");
require_once("../includes/functions.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$lastSongId = $_GET['last_song_id'] ?? null;
$genre = $_GET['genre'] ?? null;
$key = $_GET['key'] ?? null;

class AutoDJ {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate next song for auto mix
     */
    public function getNextSong($userId, $lastSongId = null, $genre = null, $key = null) {
        
        // If we have the last song, try to find compatible matches
        if ($lastSongId) {
            $lastSong = $this->getSongInfo($lastSongId);
            
            if (!$lastSong) {
                return $this->getRandomSong($userId);
            }
            
            // Try to find key-compatible song
            if ($lastSong['key_note']) {
                $nextSong = $this->findKeyCompatibleSong($userId, $lastSong['key_note'], $lastSongId);
                if ($nextSong) {
                    return $nextSong;
                }
            }
            
            // Try to find similar genre
            if ($lastSong['genre']) {
                $nextSong = $this->findGenreMatchSong($userId, $lastSong['genre'], $lastSongId);
                if ($nextSong) {
                    return $nextSong;
                }
            }
        }
        
        // If specific genre requested
        if ($genre) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM songs
                WHERE genre = ?
                AND id NOT IN (
                    SELECT song_id FROM play_history
                    WHERE user_id = ? AND played_at > DATE_SUB(NOW(), INTERVAL 1 WEEK)
                )
                ORDER BY RAND()
                LIMIT 1
            ");
            $stmt->execute([$genre, $userId]);
            $song = $stmt->fetch();
            if ($song) return $song;
        }
        
        // Fallback to random song
        return $this->getRandomSong($userId);
    }
    
    /**
     * Find key-compatible song
     */
    private function findKeyCompatibleSong($userId, $originalKey, $excludeSongId) {
        $compatibleKeys = KeyDetector::getCompatibleKeys($originalKey);
        
        $stmt = $this->pdo->prepare("
            SELECT * FROM songs
            WHERE key_note IN (" . implode(',', array_fill(0, count($compatibleKeys), '?')) . ")
            AND id != ?
            AND id NOT IN (
                SELECT song_id FROM play_history
                WHERE user_id = ? AND played_at > DATE_SUB(NOW(), INTERVAL 6 HOUR)
            )
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $params = $compatibleKeys;
        $params[] = $excludeSongId;
        $params[] = $userId;
        
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Find genre-matching song
     */
    private function findGenreMatchSong($userId, $genre, $excludeSongId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM songs
            WHERE genre = ?
            AND id != ?
            AND id NOT IN (
                SELECT song_id FROM play_history
                WHERE user_id = ? AND played_at > DATE_SUB(NOW(), INTERVAL 12 HOUR)
            )
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute([$genre, $excludeSongId, $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get random song
     */
    private function getRandomSong($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM songs
            WHERE id NOT IN (
                SELECT song_id FROM play_history
                WHERE user_id = ? AND played_at > DATE_SUB(NOW(), INTERVAL 3 DAY)
            )
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get song info
     */
    private function getSongInfo($songId) {
        $stmt = $this->pdo->prepare("SELECT * FROM songs WHERE id = ?");
        $stmt->execute([$songId]);
        return $stmt->fetch();
    }
}

$autoDJ = new AutoDJ($pdo);
$nextSong = $autoDJ->getNextSong($_SESSION['user_id'], $lastSongId, $genre, $key);

if ($nextSong) {
    logSongPlay($pdo, $_SESSION['user_id'], $nextSong['id']);
    
    echo json_encode([
        'success' => true,
        'song' => $nextSong,
        'source' => 'autoDJ'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No songs available'
    ]);
}
