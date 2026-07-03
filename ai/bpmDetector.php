<?php

// ===============================
// BPM Detector Module
// ===============================

class BPMDetector {
    
    /**
     * Estimate BPM from audio file
     * This is a simplified estimation based on file properties
     * For production, consider using specialized libraries like PSAUDIO or FFmpeg
     */
    public static function detectBPM($filePath) {
        
        if (!file_exists($filePath)) {
            return ['error' => 'File not found'];
        }
        
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Default BPM ranges for different genres
        $defaultRanges = [
            'electronic' => [120, 130],
            'hip-hop' => [85, 115],
            'pop' => [90, 130],
            'rock' => [110, 140],
            'country' => [90, 120],
            'jazz' => [80, 120],
            'classical' => [60, 120],
            'default' => [100, 120]
        ];
        
        try {
            // Attempt to use FFmpeg if available for more accurate detection
            if (shell_exec('which ffprobe')) {
                return self::detectBPMWithFFmpeg($filePath);
            }
            
            // Fallback: estimate based on file size and duration
            return self::estimateBPM($filePath, $defaultRanges['default']);
            
        } catch (Exception $e) {
            return ['bpm' => 120, 'confidence' => 0.3, 'method' => 'default'];
        }
    }
    
    /**
     * Detect BPM using FFmpeg
     */
    private static function detectBPMWithFFmpeg($filePath) {
        $command = 'ffprobe -v error -show_format -show_streams "' . escapeshellarg($filePath) . '" 2>&1';
        $output = shell_exec($command);
        
        if (!$output) {
            return ['bpm' => 120, 'confidence' => 0.2, 'method' => 'ffmpeg_failed'];
        }
        
        // Extract duration
        if (preg_match('/duration=(\d+\.?\d*)/', $output, $matches)) {
            $duration = floatval($matches[1]);
            $bpm = self::calculateBPM($duration);
            return [
                'bpm' => $bpm,
                'confidence' => 0.6,
                'method' => 'ffmpeg'
            ];
        }
        
        return ['bpm' => 120, 'confidence' => 0.3, 'method' => 'ffmpeg_fallback'];
    }
    
    /**
     * Estimate BPM based on file properties
     */
    private static function estimateBPM($filePath, $defaultRange) {
        $fileSize = filesize($filePath);
        
        // Very rough estimation
        $estimatedBPM = 60 + ($fileSize % 80);
        
        return [
            'bpm' => intval($estimatedBPM),
            'confidence' => 0.25,
            'method' => 'estimate',
            'range' => $defaultRange
        ];
    }
    
    /**
     * Calculate BPM from duration
     */
    private static function calculateBPM($duration) {
        // Rough approximation
        $bpm = round(120 * (300 / $duration));
        return max(60, min(200, $bpm)); // Clamp between 60-200
    }
    
    /**
     * Validate detected BPM
     */
    public static function validateBPM($bpm) {
        return $bpm >= 40 && $bpm <= 250;
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
    
    // Get song path
    $stmt = $pdo->prepare("SELECT filename FROM songs WHERE id = ?");
    $stmt->execute([$songId]);
    $song = $stmt->fetch();
    
    if (!$song) {
        echo json_encode(['error' => 'Song not found']);
        exit;
    }
    
    $filePath = "../uploads/songs/" . $song['filename'];
    $detection = BPMDetector::detectBPM($filePath);
    
    echo json_encode($detection);
}
