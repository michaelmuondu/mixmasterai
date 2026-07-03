<?php

// ===============================
// Recommendation Engine Module
// ===============================

class RecommendationEngine {
    
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate recommendations for a user
     */
    public function generateRecommendations($userId, $limit = 10) {
        
        // Get user's top genres and artists
        $topGenres = $this->getUserTopGenres($userId);
        $topArtists = $this->getUserTopArtists($userId);
        $topKeys = $this->getUserTopKeys($userId);
        
        // Get similar songs
        $recommendations = [];
        
        // 1. Genre-based recommendations
        if (!empty($topGenres)) {
            $genreRecs = $this->getGenreBasedRecommendations($userId, $topGenres, 5);
            $recommendations = array_merge($recommendations, $genreRecs);
        }
        
        // 2. Artist-based recommendations
        if (!empty($topArtists)) {
            $artistRecs = $this->getArtistBasedRecommendations($userId, $topArtists, 3);
            $recommendations = array_merge($recommendations, $artistRecs);
        }
        
        // 3. Key compatibility recommendations
        if (!empty($topKeys)) {
            $keyRecs = $this->getKeyBasedRecommendations($userId, $topKeys, 2);
            $recommendations = array_merge($recommendations, $keyRecs);
        }
        
        // Deduplicate and sort by score
        $recommendations = $this->deduplicateRecommendations($recommendations);
        uasort($recommendations, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Limit results
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get user's top genres
     */
    private function getUserTopGenres($userId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT s.genre, COUNT(*) as count
            FROM play_history ph
            JOIN songs s ON ph.song_id = s.id
            WHERE ph.user_id = ? AND s.genre IS NOT NULL
            GROUP BY s.genre
            ORDER BY count DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['genre']] = $row['count'];
        }
        return $results;
    }
    
    /**
     * Get user's top artists
     */
    private function getUserTopArtists($userId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT s.artist, COUNT(*) as count
            FROM play_history ph
            JOIN songs s ON ph.song_id = s.id
            WHERE ph.user_id = ? AND s.artist IS NOT NULL
            GROUP BY s.artist
            ORDER BY count DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['artist']] = $row['count'];
        }
        return $results;
    }
    
    /**
     * Get user's top keys
     */
    private function getUserTopKeys($userId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT s.key_note, COUNT(*) as count
            FROM play_history ph
            JOIN songs s ON ph.song_id = s.id
            WHERE ph.user_id = ? AND s.key_note IS NOT NULL
            GROUP BY s.key_note
            ORDER BY count DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['key_note']] = $row['count'];
        }
        return $results;
    }
    
    /**
     * Get genre-based recommendations
     */
    private function getGenreBasedRecommendations($userId, $topGenres, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.title, s.artist, s.genre, COUNT(ph.id) as popularity
            FROM songs s
            LEFT JOIN play_history ph ON s.id = ph.song_id
            WHERE s.genre IN (" . implode(',', array_fill(0, count($topGenres), '?')) . ")
            AND s.id NOT IN (
                SELECT song_id FROM play_history WHERE user_id = ?
            )
            GROUP BY s.id
            ORDER BY popularity DESC
            LIMIT ?
        ");
        
        $params = array_keys($topGenres);
        $params[] = $userId;
        $params[] = $limit;
        
        $stmt->execute($params);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'reason' => 'Similar to ' . $row['genre'],
                'score' => 80 + ($row['popularity'] * 0.1)
            ];
        }
        return $results;
    }
    
    /**
     * Get artist-based recommendations
     */
    private function getArtistBasedRecommendations($userId, $topArtists, $limit = 3) {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.title, s.artist, COUNT(ph.id) as popularity
            FROM songs s
            LEFT JOIN play_history ph ON s.id = ph.song_id
            WHERE s.artist IN (" . implode(',', array_fill(0, count($topArtists), '?')) . ")
            AND s.id NOT IN (
                SELECT song_id FROM play_history WHERE user_id = ?
            )
            GROUP BY s.id
            ORDER BY popularity DESC
            LIMIT ?
        ");
        
        $params = array_keys($topArtists);
        $params[] = $userId;
        $params[] = $limit;
        
        $stmt->execute($params);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'reason' => 'From ' . $row['artist'],
                'score' => 70 + ($row['popularity'] * 0.1)
            ];
        }
        return $results;
    }
    
    /**
     * Get key-based recommendations for mixing
     */
    private function getKeyBasedRecommendations($userId, $topKeys, $limit = 2) {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.title, s.artist, s.key_note, COUNT(ph.id) as popularity
            FROM songs s
            LEFT JOIN play_history ph ON s.id = ph.song_id
            WHERE s.key_note IN (" . implode(',', array_fill(0, count($topKeys), '?')) . ")
            AND s.id NOT IN (
                SELECT song_id FROM play_history WHERE user_id = ?
            )
            GROUP BY s.id
            ORDER BY popularity DESC
            LIMIT ?
        ");
        
        $params = array_keys($topKeys);
        $params[] = $userId;
        $params[] = $limit;
        
        $stmt->execute($params);
        
        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'reason' => 'Compatible key (' . $row['key_note'] . ')',
                'score' => 75
            ];
        }
        return $results;
    }
    
    /**
     * Deduplicate recommendations
     */
    private function deduplicateRecommendations($recommendations) {
        $seen = [];
        $deduped = [];
        
        foreach ($recommendations as $rec) {
            if (!isset($seen[$rec['id']])) {
                $seen[$rec['id']] = true;
                $deduped[$rec['id']] = $rec;
            }
        }
        
        return $deduped;
    }
    
    /**
     * Save recommendation to database
     */
    public function saveRecommendation($userId, $songId, $score, $reason = '') {
        $stmt = $this->pdo->prepare("
            INSERT INTO recommendations (user_id, song_id, score, reason)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE score = ?, reason = ?
        ");
        
        return $stmt->execute([$userId, $songId, $score, $reason, $score, $reason]);
    }
}

// Handle API request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    require_once("../includes/db.php");
    
    $engine = new RecommendationEngine($pdo);
    $recommendations = $engine->generateRecommendations($_SESSION['user_id'], 10);
    
    // Save recommendations
    foreach ($recommendations as $rec) {
        $engine->saveRecommendation($_SESSION['user_id'], $rec['id'], $rec['score'], $rec['reason']);
    }
    
    echo json_encode([
        'success' => true,
        'recommendations' => $recommendations
    ]);
}
