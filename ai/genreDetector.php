<?php

// ===============================
// Genre Detector Module
// ===============================

class GenreDetector {
    
    private static $genreKeywords = [
        'electronic' => ['synth', 'beat', 'loop', 'drum machine', 'electronic', 'edm'],
        'hip-hop' => ['rap', 'hip hop', 'beat', 'urban', 'hiphop'],
        'pop' => ['pop', 'catchy', 'chorus', 'upbeat', 'mainstream'],
        'rock' => ['guitar', 'rock', 'band', 'hard', 'metal'],
        'jazz' => ['jazz', 'improvisation', 'swing', 'trumpet', 'saxophone'],
        'classical' => ['orchestra', 'symphony', 'classical', 'strings', 'piano'],
        'country' => ['country', 'western', 'acoustic', 'twang', 'rural'],
        'reggae' => ['reggae', 'dub', 'rasta', 'jamaica', 'ska'],
        'blues' => ['blues', 'soul', 'emotional', 'deep'],
        'latin' => ['latin', 'salsa', 'cumbia', 'bossa', 'tropical']
    ];
    
    /**
     * Detect genre from metadata and filename
     */
    public static function detectGenre($filename, $artist = '', $bpm = null) {
        $scores = [];
        
        $textToAnalyze = strtolower($filename . ' ' . $artist);
        
        foreach (self::$genreKeywords as $genre => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($textToAnalyze, $keyword) !== false) {
                    $score += 10;
                }
            }
            $scores[$genre] = $score;
        }
        
        // Consider BPM for genre estimation
        if ($bpm !== null) {
            $scores = self::adjustScoresByBPM($scores, $bpm);
        }
        
        // Sort by score
        arsort($scores);
        
        $topGenre = key($scores);
        $confidence = ($scores[$topGenre] / 100);
        
        return [
            'genre' => $topGenre,
            'confidence' => min($confidence, 1.0),
            'top_3' => array_slice($scores, 0, 3),
            'method' => 'keyword_matching'
        ];
    }
    
    /**
     * Adjust scores based on BPM
     */
    private static function adjustScoresByBPM(&$scores, $bpm) {
        // Fast BPM (120+) -> more likely electronic, pop, EDM
        if ($bpm >= 120) {
            $scores['electronic'] += 20;
            $scores['pop'] += 15;
        }
        // Slow BPM (60-90) -> more likely jazz, blues, classical
        elseif ($bpm <= 90) {
            $scores['jazz'] += 20;
            $scores['blues'] += 15;
            $scores['classical'] += 15;
        }
        // Medium BPM -> hip-hop, rock
        else {
            $scores['hip-hop'] += 15;
            $scores['rock'] += 15;
        }
        
        return $scores;
    }
    
    /**
     * Get all available genres
     */
    public static function getAvailableGenres() {
        return array_keys(self::$genreKeywords);
    }
    
    /**
     * Validate genre
     */
    public static function validateGenre($genre) {
        return isset(self::$genreKeywords[$genre]);
    }
}

// Handle API request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    require_once("../includes/db.php");
    
    $songId = $_POST['song_id'] ?? null;
    
    if (!$songId) {
        echo json_encode(['error' => 'Song ID required']);
        exit;
    }
    
    // Get song info
    $stmt = $pdo->prepare("SELECT title, artist, filename FROM songs WHERE id = ?");
    $stmt->execute([$songId]);
    $song = $stmt->fetch();
    
    if (!$song) {
        echo json_encode(['error' => 'Song not found']);
        exit;
    }
    
    // Get BPM if available
    $stmt = $pdo->prepare("SELECT bpm FROM songs WHERE id = ?");
    $stmt->execute([$songId]);
    $bpmData = $stmt->fetch();
    $bpm = $bpmData['bpm'] ?? null;
    
    $detection = GenreDetector::detectGenre($song['filename'], $song['artist'], $bpm);
    
    echo json_encode($detection);
}
