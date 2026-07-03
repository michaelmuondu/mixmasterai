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

        <div class="upload-card">

            <form id="uploadForm" enctype="multipart/form-data">

                <div class="drop-area" id="dropArea">

                    <h2>🎵 Drag & Drop Music Here</h2>

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

                <br>

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
                    name="genre"
                    placeholder="Genre"
                >

                <br><br>

                <button
                    class="btn"
                    type="submit"
                >
                    Upload Song
                </button>

            </form>

            <br>

            <div id="progressContainer">

                <div id="progressBar"></div>

            </div>

            <br>

            <div id="message"></div>

        </div>

    </div>

</div">
<script src="../assets/scripts.js"></script>

<?php require_once("../includes/footer.php"); ?>