<?php

require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<div class="container">

    <?php require_once("../includes/sidebar.php"); ?>

    <div class="content">

        <h1>Dashboard</h1>

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
            <div class="card">
                <h2>👤 Welcome</h2>
                <p><?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
            </div>

        </div>

        <div class="welcome-box">

            <h2>Welcome to MixMaster AI</h2>

            <p>
                Upload your music library and let the AI create professional DJ mixes.
            </p>

        </div>

    </div>

</div>

<?php require_once("../includes/footer.php"); ?>
<script src="../assets/dashboard.js"></script>