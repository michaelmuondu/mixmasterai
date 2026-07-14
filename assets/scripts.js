console.log("✅ MixMaster AI Loaded");

const browseBtn = document.getElementById("browseBtn");
const songInput = document.getElementById("song");
const form = document.getElementById("uploadForm");
const message = document.getElementById("message");
const progressBar = document.getElementById("progressBar");
const dropArea = document.getElementById("dropArea");

// Only run this code on the Upload page
if (form) {

    console.log("✅ Upload page detected");

    browseBtn?.addEventListener("click", () => {
        songInput.click();
    });

    dropArea?.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragging");
    });

    dropArea?.addEventListener("dragleave", () => {
        dropArea.classList.remove("dragging");
    });

    dropArea?.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragging");

        if (e.dataTransfer.files.length > 0) {
            songInput.files = e.dataTransfer.files;
        }
    });
form.addEventListener("submit", function (e) {

    e.preventDefault();

    const data = new FormData();

    // IMPORTANT: manually append file
    data.append("song", songInput.files[0]);

    data.append("title", document.querySelector("[name='title']").value);
    data.append("artist", document.querySelector("[name='artist']").value);
    data.append("album", document.querySelector("[name='album']").value);
    data.append("genre", document.querySelector("[name='genre']").value);

    const xhr = new XMLHttpRequest();

    xhr.open("POST", "../api/uploadSong.php", true);

    xhr.onload = function () {

        console.log(xhr.responseText);

        const res = JSON.parse(xhr.responseText);

        message.innerHTML = res.message;

        if (res.success) {
            form.reset();
            progressBar.style.width = "0%";
        }

    };

    xhr.send(data);
});

const importBtn = document.getElementById("importSpotifyBtn");
const spotifyUrlInput = document.getElementById("spotifyUrl");
const spotifyMessage = document.getElementById("spotifyMessage");

if (importBtn) {
    importBtn.addEventListener("click", async () => {
        const url = spotifyUrlInput.value.trim();
        if (!url) {
            spotifyMessage.textContent = "Paste a Spotify album or playlist URL first.";
            spotifyMessage.style.color = "#facc15";
            return;
        }

        importBtn.disabled = true;
        importBtn.textContent = "Importing...";
        spotifyMessage.textContent = "";

        try {
            const response = await fetch("../api/importSpotify.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({ spotify_url: url })
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                spotifyMessage.textContent = `Unexpected server response: ${text}`;
                spotifyMessage.style.color = "#f87171";
                console.error('ImportSpotify decode', parseErr, text);
                return;
            }

            spotifyMessage.textContent = data.message || 'Unexpected response from import endpoint.';
            spotifyMessage.style.color = data.success ? "#4ade80" : "#f87171";

            if (data.success) {
                spotifyUrlInput.value = "";
            }
        } catch (err) {
            spotifyMessage.textContent = "Unable to import Spotify album. Check your network or API credentials.";
            spotifyMessage.style.color = "#f87171";
            console.error(err);
        } finally {
            importBtn.disabled = false;
            importBtn.textContent = "Import from Spotify";
        }
    });
}
