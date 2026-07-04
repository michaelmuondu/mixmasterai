<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/player.css">

<div class="container">

<?php require_once("../includes/sidebar.php"); ?>

<div class="content dj-console-page">

    <div class="console-header">

        <h1>🎧 MixMaster AI DJ Console</h1>

        <div class="master-controls">

            <button id="recordMix">⏺ Record</button>

            <button id="stopRecord">⏹ Stop</button>

        </div>

    </div>

    <div class="dj-console">

        <!-- ========================= -->
        <!-- Deck A -->
        <!-- ========================= -->

        <div class="deck" id="deckA">

            <div class="deck-title">

                <h2>Deck A</h2>

                <span id="bpmA">-- BPM</span>

            </div>

            <div class="cover-art">

                <img
                    id="coverA"
                    src="../assets/images/default-cover.png"
                    alt="Cover">

            </div>

            <h3 id="trackA">

                No Track Loaded

            </h3>

            <p class="artist">

                <span id="artistA">

                    Unknown Artist

                </span>

            </p>

            <div class="track-meta">

                <span id="genreA">--</span>

                <span id="keyA">--</span>

                <span id="durationLabelA">00:00</span>

            </div>

            <canvas
                id="waveformA"
                width="600"
                height="120">
            </canvas>

            <div class="vu-meter">

                <div
                    id="vuA"
                    class="vu-bar">
                </div>

            </div>

            <div class="time-display">

                <span id="currentA">

                    00:00

                </span>

                <input
                    type="range"
                    id="seekA"
                    min="0"
                    max="100"
                    value="0">

                <span id="durationA">

                    00:00

                </span>

            </div>

            <div class="transport">

                <button id="loadA">

                    Load

                </button>

                <button id="playA">

                    ▶

                </button>

                <button id="pauseA">

                    ⏸

                </button>

                <button id="stopA">

                    ⏹

                </button>

                <button id="syncA">

                    SYNC

                </button>

            </div>

            <div class="sliders">

                <label>

                    Volume

                </label>

                <input
                    type="range"
                    id="volumeA"
                    min="0"
                    max="1"
                    step="0.01"
                    value="1">

                <label>

                    Pitch

                </label>

                <input
                    type="range"
                    id="pitchA"
                    min="-50"
                    max="50"
                    value="0">

                <span id="pitchValueA">

                    0%

                </span>

            </div>

            <audio id="audioA"></audio>

        </div>

        <!-- ========================= -->
        <!-- Mixer -->
        <!-- ========================= -->

        <div class="mixer">

            <h2>Mixer</h2>

            <label>Crossfader</label>

            <input
                type="range"
                id="crossfader"
                min="0"
                max="100"
                value="50">

            <hr>

            <label>Bass</label>

            <input
                type="range"
                id="bass"
                min="-20"
                max="20"
                value="0">

            <label>Mid</label>

            <input
                type="range"
                id="mid"
                min="-20"
                max="20"
                value="0">

            <label>Treble</label>

            <input
                type="range"
                id="treble"
                min="-20"
                max="20"
                value="0">

        </div>

        <!-- ========================= -->
        <!-- Deck B -->
        <!-- ========================= -->

        <div class="deck" id="deckB">

            <div class="deck-title">

                <h2>Deck B</h2>

                <span id="bpmB">-- BPM</span>

            </div>

            <div class="cover-art">

                <img
                    id="coverB"
                    src="../assets/images/default-cover.png"
                    alt="Cover">

            </div>

            <h3 id="trackB">

                No Track Loaded

            </h3>

            <p class="artist">

                <span id="artistB">

                    Unknown Artist

                </span>

            </p>

            <div class="track-meta">

                <span id="genreB">--</span>

                <span id="keyB">--</span>

                <span id="durationLabelB">00:00</span>

            </div>

            <canvas
                id="waveformB"
                width="600"
                height="120">
            </canvas>

            <div class="vu-meter">

                <div
                    id="vuB"
                    class="vu-bar">
                </div>

            </div>

            <div class="time-display">

                <span id="currentB">

                    00:00

                </span>

                <input
                    type="range"
                    id="seekB"
                    min="0"
                    max="100"
                    value="0">

                <span id="durationB">

                    00:00

                </span>

            </div>

            <div class="transport">

                <button id="loadB">Load</button>

                <button id="playB">▶</button>

                <button id="pauseB">⏸</button>

                <button id="stopB">⏹</button>

                <button id="syncB">SYNC</button>

            </div>

            <div class="sliders">

                <label>Volume</label>

                <input
                    type="range"
                    id="volumeB"
                    min="0"
                    max="1"
                    step="0.01"
                    value="1">

                <label>Pitch</label>

                <input
                    type="range"
                    id="pitchB"
                    min="-50"
                    max="50"
                    value="0">

                <span id="pitchValueB">

                    0%

                </span>

            </div>

            <audio id="audioB"></audio>

        </div>

    </div>
        <!-- ================================= -->
    <!-- MUSIC BROWSER -->
    <!-- ================================= -->

    <div class="music-browser">

        <div class="browser-header">

            <h2>🎵 Music Library</h2>

            <input
                type="text"
                id="searchSongs"
                placeholder="Search title, artist, genre...">

        </div>

        <div class="browser-filters">

            <select id="genreFilter">

                <option value="">All Genres</option>

            </select>

            <select id="bpmFilter">

                <option value="">All BPM</option>

                <option value="0-90">0 - 90</option>

                <option value="91-110">91 - 110</option>

                <option value="111-130">111 - 130</option>

                <option value="131-150">131 - 150</option>

                <option value="151-300">151+</option>

            </select>

            <button id="refreshLibrary">

                Refresh

            </button>

        </div>

        <div id="playlist" class="playlist">

            Loading music library...

        </div>

    </div>

</div>

</div>

<script src="../assets/audioEngine.js"></script>

<script src="../assets/waveform.js"></script>

<script src="../assets/player.js"></script>

<?php require_once("../includes/footer.php"); ?>