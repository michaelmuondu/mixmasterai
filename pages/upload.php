<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<div class="container">

    <?php require_once("../includes/sidebar.php"); ?>

    <div class="content">

        <div class="page-header">
            <h1>Upload Music</h1>
            <p>Add MP3 or WAV files to your music library.</p>
        </div>

        <div class="upload-grid">

            <div class="upload-card">

                <h2>Upload Local Track</h2>

                <form id="uploadForm" enctype="multipart/form-data">

                    <div class="drop-area" id="dropArea">

                        <h3>🎵 Drag & Drop Music Here</h3>

                        <p>or</p>

                        <input
                            type="file"
                            id="song"
                            name="song"
                            accept=".mp3,.wav"
                            hidden
                        >

                        <button
                            type="button"
                            id="browseBtn"
                            class="btn"
                        >
                            Browse Files
                        </button>

                    </div>

                    <input
                        type="text"
                        name="title"
                        placeholder="Song Title"
                        required
                    >

                    <input
                        type="text"
                        name="artist"
                        placeholder="Artist"
                    >

                    <input
                        type="text"
                        name="album"
                        placeholder="Album"
                    >

                    <input
                        type="text"
                        name="genre"
                        placeholder="Genre"
                    >

                    <button
                        class="btn"
                        type="submit"
                    >
                        Upload Song
                    </button>

                </form>

                <div id="progressContainer">
                    <div id="progressBar"></div>
                </div>

                <div id="message"></div>

            </div>

            <div class="import-card">

                <h2>Import Spotify Album</h2>

                <p>Paste a Spotify album or playlist URL to import available preview tracks directly into your library.</p>

                <input
                    type="url"
                    id="spotifyUrl"
                    placeholder="https://open.spotify.com/album/..."
                >

                <button
                    id="importSpotifyBtn"
                    type="button"
                    class="btn"
                >
                    Import from Spotify
                </button>

                <div id="spotifyMessage"></div>

            </div>

        </div>

    </div>

</div>
<script src="../assets/scripts.js"></script>

<?php require_once("../includes/footer.php"); ?>