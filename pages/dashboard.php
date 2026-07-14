<?php

require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<div class="container">

    <?php require_once("../includes/sidebar.php"); ?>

    <div class="content">

        <div class="dashboard-header">
            <div>
                <h1>MixMaster AI Dashboard</h1>
                <p>Manage your tracks, launch the DJ console, and import Spotify albums with immersive DJ-style visuals.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn" href="player.php">Open DJ Console</a>
                <a class="btn secondary" href="upload.php">Upload Music</a>
            </div>
        </div>

        <div class="cards">

            <div class="card">
                <h2>🎵 Songs</h2>
                <p id="songsCount">0</p>
            </div>

            <div class="card">
                <h2>📃 Playlists</h2>
                <p id="playlistsCount">0</p>
            </div>

            <div class="card">
                <h2>🎧 AI Mixes</h2>
                <p id="mixCount">0</p>
            </div>

            <div class="card">
                <h2>💾 Storage</h2>
                <p id="storageCount">0 MB</p>
            </div>

            <div class="card highlight-card">
                <h2>👤 Welcome</h2>
                <p><?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
            </div>

        </div>

        <div class="dashboard-split">

            <div class="welcome-box">
                <h2>Live DJ Experience</h2>
                <p>Drag and drop tracks, import Spotify albums, and build an immersive DJ library with instantly available preview streams.</p>
            </div>

            <div class="insights-card">
                <h2>Quick Actions</h2>
                <ul>
                    <li>Upload local songs and manage your audio library.</li>
                    <li>Import Spotify albums or playlists with preview support.</li>
                    <li>Launch the DJ console to mix tracks live.</li>
                </ul>
            </div>

        </div>

    </div>

</div>

<?php require_once("../includes/footer.php"); ?>
<script src="../assets/dashboard.js"></script>