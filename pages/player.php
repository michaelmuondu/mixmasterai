<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>
<link rel="stylesheet" href="assets/player.css">
<div class="container">

<?php require_once("../includes/sidebar.php"); ?>

<div class="content">

<h1>🎧 MixMaster AI DJ Console</h1>

<div class="dj-console">

    <!-- Deck A -->
    <div class="deck">

        <h2>Deck A</h2>

        <div class="waveform">
            Waveform will appear here
        </div>

        <h3 id="trackA">No Track Loaded</h3>

        <audio id="audioA"></audio>

        <div class="controls">

            <button id="loadA">Load</button>

            <button id="playA">▶ Play</button>

            <button id="pauseA">⏸ Pause</button>

            <button id="stopA">⏹ Stop</button>

        </div>

        <label>Volume</label>

        <input
            type="range"
            id="volumeA"
            min="0"
            max="1"
            step="0.01"
            value="1">

    </div>

    <!-- Mixer -->

    <div class="mixer">

        <h2>Mixer</h2>

        <label>Crossfader</label>

        <input
            type="range"
            id="crossfader"
            min="0"
            max="100"
            value="50">

    </div>

    <!-- Deck B -->

    <div class="deck">

        <h2>Deck B</h2>

        <div class="waveform">
            Waveform will appear here
        </div>

        <h3 id="trackB">No Track Loaded</h3>

        <audio id="audioB"></audio>

        <div class="controls">

            <button id="loadB">Load</button>

            <button id="playB">▶ Play</button>

            <button id="pauseB">⏸ Pause</button>

            <button id="stopB">⏹ Stop</button>

        </div>

        <label>Volume</label>

        <input
            type="range"
            id="volumeB"
            min="0"
            max="1"
            step="0.01"
            value="1">

    </div>

</div>

<div class="playlist-panel">

<h2>Playlist</h2>

<div id="playlist">

No songs loaded.

</div>

</div>

</div>

</div>

<link rel="stylesheet" href="assets/player.css">
<script src="assets/player.js"></script>

<?php require_once("../includes/footer.php"); ?>