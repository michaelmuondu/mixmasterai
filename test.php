<?php

echo "<h1>🎧 MixMaster AI - System Test</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    require_once 'includes/db.php';
    echo "✅ Database connection successful!";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
    exit;
}

// Test 2: Check Tables
echo "<h2>2. Database Tables</h2>";
$tables = ['users', 'songs', 'playlists', 'playlist_songs', 'play_history', 'user_settings', 'recommendations'];
foreach ($tables as $table) {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    if ($stmt->fetch()) {
        echo "✅ Table `$table` exists<br>";
    } else {
        echo "❌ Table `$table` missing - run database.sql to create it<br>";
    }
}

// Test 3: Upload Directory
echo "<h2>3. File Uploads Directory</h2>";
$uploadDir = __DIR__ . '/uploads/songs/';
if (is_dir($uploadDir)) {
    echo "✅ Upload directory exists: $uploadDir<br>";
} else {
    echo "⚠️ Upload directory missing, attempting to create...<br>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "✅ Created upload directory<br>";
    } else {
        echo "❌ Failed to create upload directory<br>";
    }
}

// Test 4: Helper Functions
echo "<h2>4. Helper Functions</h2>";
require_once 'includes/functions.php';
if (function_exists('formatTime')) {
    echo "✅ Helper functions loaded<br>";
    echo "   - formatTime(120) = " . formatTime(120) . "<br>";
    echo "   - formatFileSize(1048576) = " . formatFileSize(1048576) . "<br>";
} else {
    echo "❌ Helper functions not found<br>";
}

// Test 5: AI Modules
echo "<h2>5. AI Detection Modules</h2>";
try {
    require_once 'ai/bpmDetector.php';
    if (class_exists('BPMDetector')) {
        echo "✅ BPMDetector loaded<br>";
    }
} catch (Exception $e) {
    echo "❌ BPMDetector error: " . $e->getMessage() . "<br>";
}

try {
    require_once 'ai/genreDetector.php';
    if (class_exists('GenreDetector')) {
        echo "✅ GenreDetector loaded<br>";
    }
} catch (Exception $e) {
    echo "❌ GenreDetector error: " . $e->getMessage() . "<br>";
}

try {
    require_once 'ai/keyDetector.php';
    if (class_exists('KeyDetector')) {
        echo "✅ KeyDetector loaded<br>";
    }
} catch (Exception $e) {
    echo "❌ KeyDetector error: " . $e->getMessage() . "<br>";
}

// Test 6: API Endpoints
echo "<h2>6. API Endpoints</h2>";
$apis = [
    'api/getsongs.php',
    'api/dashboardStats.php',
    'api/nextsong.php',
    'api/autoDJ.php',
    'api/saveplaylist.php',
    'api/uploadsong.php',
    'api/deleteSong.php'
];

foreach ($apis as $api) {
    if (file_exists($api)) {
        echo "✅ API endpoint exists: $api<br>";
    } else {
        echo "❌ API endpoint missing: $api<br>";
    }
}

// Test 7: Page Files
echo "<h2>7. Page Files</h2>";
$pages = [
    'pages/login.php',
    'pages/register.php',
    'pages/dashboard.php',
    'pages/library.php',
    'pages/player.php',
    'pages/upload.php',
    'pages/playlists.php',
    'pages/settings.php',
    'pages/logout.php'
];

foreach ($pages as $page) {
    if (file_exists($page)) {
        echo "✅ Page file exists: $page<br>";
    } else {
        echo "❌ Page file missing: $page<br>";
    }
}

// Test 8: Asset Files
echo "<h2>8. Asset Files</h2>";
$assets = [
    'assets/styles.css',
    'assets/player.css',
    'assets/dashboard.js',
    'assets/library.js',
    'assets/player.js',
    'assets/scripts.js'
];

foreach ($assets as $asset) {
    if (file_exists($asset)) {
        $size = filesize($asset);
        echo "✅ Asset file exists: $asset ($size bytes)<br>";
    } else {
        echo "❌ Asset file missing: $asset<br>";
    }
}

echo "<hr>";
echo "<h2>✅ System Test Complete!</h2>";
echo "<p>Next steps:</p>";
echo "<ol>";
echo "<li>Import the database schema: <code>database/database.sql</code></li>";
echo "<li>Create a test user via registration</li>";
echo "<li>Navigate to <a href='pages/dashboard.php'>/pages/dashboard.php</a></li>";
echo "</ol>";

?>