<?php

// ===============================
// Musical Key Detector Module
// ===============================

class KeyDetector {
    
    private static $musicalKeys = [
        'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'
    ];
    
    private static $keyCompatibility = [
        'C' => ['C', 'G', 'F', 'A', 'D', 'E'],
        'C#' => ['C#', 'G#', 'F#', 'A#', 'D#', 'F'],
        'D' => ['D', 'A', 'G', 'B', 'E', 'F#'],
        'D#' => ['D#', 'A#', 'G#', 'C', 'F', 'G'],
        'E' => ['E', 'B', 'A', 'C#', 'F#', 'G#'],
        'F' => ['F', 'C', 'B', 'D', 'G', 'A'],
        'F#' => ['F#', 'C#', 'B', 'D#', 'G#', 'A#'],
        'G' => ['G', 'D', 'C', 'E', 'A', 'B'],
        'G#' => ['G#', 'D#', 'C#', 'F', 'A#', 'C'],
        'A' => ['A', 'E', 'D', 'F#', 'B', 'C#'],
        'A#' => ['A#', 'F', 'D#', 'G', 'C', 'D'],
        'B' => ['B', 'F#', 'E', 'G#', 'C#', 'D#']
    ];
    
    /**
     * Detect musical key from audio file
     * This is a simplified detection based on filename and metadata
     */
    public static function detectKey($filename, $bpm = null) {
        
        // Try to extract key from filename patterns
        $detectedKey = self::extractKeyFromFilename($filename);
        
        if ($detectedKey) {
            return [
                'key' => $detectedKey,
                'confidence' => 0.8,
                'method' => 'filename_extraction'
            ];
        }
        
        // Fallback: generate from hash for consistency
        $suggestedKey = self::generateConsistentKey($filename);
        
        return [
            'key' => $suggestedKey,
            'confidence' => 0.3,
            'method' => 'hash_generation',
            'compatible_keys' => self::getCompatibleKeys($suggestedKey)
        ];
    }
    
    /**
     * Extract key from filename if present
     */
    private static function extractKeyFromFilename($filename) {
        $filename = strtoupper($filename);
        
        foreach (self::$musicalKeys as $key) {
            if (strpos($filename, $key) !== false) {
                return $key;
            }
        }
        
        return null;
    }
    
    /**
     * Generate consistent key based on filename
     */
    private static function generateConsistentKey($filename) {
        $hash = crc32($filename);
        $index = abs($hash) % count(self::$musicalKeys);
        return self::$musicalKeys[$index];
    }
    
    /**
     * Get compatible keys for mixing
     */
    public static function getCompatibleKeys($key) {
        return self::$keyCompatibility[$key] ?? [];
    }
    
    /**
     * Calculate key distance for mixing (Camelot wheel)
     */
    public static function calculateKeyDistance($key1, $key2) {
        $keys = self::$musicalKeys;
        $pos1 = array_search($key1, $keys);
        $pos2 = array_search($key2, $keys);
        
        if ($pos1 === false || $pos2 === false) {
            return null;
        }
        
        $distance = abs($pos1 - $pos2);
        $distance = min($distance, count($keys) - $distance);
        
        return $distance;
    }
    
    /**
     * Check if two keys are compatible for mixing
     */
    public static function areKeysCompatible($key1, $key2, $tolerance = 2) {
        $distance = self::calculateKeyDistance($key1, $key2);
        return $distance !== null && $distance <= $tolerance;
    }
    
    /**
     * Get all available keys
     */
    public static function getAvailableKeys() {
        return self::$musicalKeys;
    }
    
    /**
     * Validate key
     */
    public static function validateKey($key) {
        return in_array($key, self::$musicalKeys);
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
    $stmt = $pdo->prepare("SELECT filename, bpm FROM songs WHERE id = ?");
    $stmt->execute([$songId]);
    $song = $stmt->fetch();
    
    if (!$song) {
        echo json_encode(['error' => 'Song not found']);
        exit;
    }
    
    $detection = KeyDetector::detectKey($song['filename'], $song['bpm']);
    
    echo json_encode($detection);
}
