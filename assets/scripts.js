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
}