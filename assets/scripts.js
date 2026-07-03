console.log("✅ MixMaster AI Loaded");

const browseBtn = document.getElementById("browseBtn");
const songInput = document.getElementById("song");
const form = document.getElementById("uploadForm");
const message = document.getElementById("message");
const progressBar = document.getElementById("progressBar");
const dropArea = document.getElementById("dropArea");

console.log("Browse Button:", browseBtn);
console.log("Song Input:", songInput);
console.log("Upload Form:", form);
console.log("Message Div:", message);
console.log("Progress Bar:", progressBar);
console.log("Drop Area:", dropArea);

if (!form) {
    console.error("❌ uploadForm was not found.");
} else {

    console.log("✅ Form Found");

    if (browseBtn) {
        browseBtn.addEventListener("click", () => {
            songInput.click();
        });
    }

    if (dropArea) {

        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("dragging");
        });

        dropArea.addEventListener("dragleave", () => {
            dropArea.classList.remove("dragging");
        });

        dropArea.addEventListener("drop", (e) => {

            e.preventDefault();

            dropArea.classList.remove("dragging");

            if (e.dataTransfer.files.length > 0) {
                songInput.files = e.dataTransfer.files;
            }

        });

    }

    form.addEventListener("submit", function (e) {

        e.preventDefault();

        console.log("✅ Submit intercepted");

        const data = new FormData(form);

        const xhr = new XMLHttpRequest();

        xhr.open("POST", "../api/uploadSong.php", true);

        xhr.upload.addEventListener("progress", function (e) {

            if (e.lengthComputable) {

                const percent = (e.loaded / e.total) * 100;

                progressBar.style.width = percent + "%";

                console.log("Uploading:", Math.round(percent) + "%");

            }

        });

        xhr.onload = function () {

            console.log("Server Response:");
            console.log(xhr.responseText);

            try {

                const response = JSON.parse(xhr.responseText);

                message.innerHTML = response.message;

                if (response.success) {

                    form.reset();

                    progressBar.style.width = "0%";

                }

            } catch (err) {

                console.error("JSON Error:", err);
                message.innerHTML = "Server returned invalid data.";

            }

        };

        xhr.onerror = function () {

            console.error("AJAX Request Failed");
            message.innerHTML = "AJAX request failed.";

        };

        xhr.send(data);

    });

}