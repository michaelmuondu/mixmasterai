<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>
<div class="container">

<?php require_once("../includes/sidebar.php"); ?>

<div class="content">

<h1>🎧 MixMaster AI DJ Console</h1>

<div class="dj-console">

    <!-- Deck A -->
    <div class="deck">

        <h2>Deck A</h2>

<div class="waveform">
    <canvas id="waveformA" width="500" height="120"></canvas>
</div>
<div class="vu-meter">
    <div id="vuA" class="vu-bar"></div>
</div>

        <h3 id="trackA">No Track Loaded</h3>

        <audio id="audioA"></audio>

        <div class="time">

        <span id="currentA">00:00</span>

    <input
        type="range"
        id="seekA"
        min="0"
        max="100"
        value="0">

        <span id="durationA">00:00</span>

</div>

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
        <canvas id="waveformB" width="500" height="120"></canvas>
    </div>

    <div class="vu-meter">
        <div id="vuB" class="vu-bar"></div>
    </div>

    <h3 id="trackB">No Track Loaded</h3>

    <audio id="audioB"></audio>

    <div class="time">

        <span id="currentB">00:00</span>

        <input
            type="range"
            id="seekB"
            min="0"
            max="100"
            value="0">

        <span id="durationB">00:00</span>

    </div>

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

<div class="playlist-panel">

<h2>Playlist</h2>

<div id="playlist">

Loading playlist...

</div>
</div>

<link rel="stylesheet" href="../assets/player.css">
<script src="../assets/player.js"></script>

<?php require_once("../includes/footer.php"); ?>
