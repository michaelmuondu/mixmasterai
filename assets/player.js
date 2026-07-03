// ===============================
// MixMaster AI Player
// ===============================

// Audio Elements
const audioA = document.getElementById("audioA");
const audioB = document.getElementById("audioB");

// Track Labels
const trackA = document.getElementById("trackA");
const trackB = document.getElementById("trackB");

// Buttons
const playA = document.getElementById("playA");
const pauseA = document.getElementById("pauseA");
const stopA = document.getElementById("stopA");

const playB = document.getElementById("playB");
const pauseB = document.getElementById("pauseB");
const stopB = document.getElementById("stopB");
playA.onclick = async function () {

    await audioContext.resume();

    audioA.play();

};

pauseA.onclick = function () {

    audioA.pause();

};

stopA.onclick = function () {

    audioA.pause();
    audioA.currentTime = 0;

};

playB.onclick = async function () {

    await audioContext.resume();

    audioB.play();

};

pauseB.onclick = function () {

    audioB.pause();

};

stopB.onclick = function () {

    audioB.pause();
    audioB.currentTime = 0;

};
// Volume
const volumeA = document.getElementById("volumeA");
const volumeB = document.getElementById("volumeB");

// Crossfader
const crossfader = document.getElementById("crossfader");

// Seek Bars
const seekA = document.getElementById("seekA");
const seekB = document.getElementById("seekB");

// Playlist
const playlist = document.getElementById("playlist");

// Waveforms
const canvasA = document.getElementById("waveformA");
const canvasB = document.getElementById("waveformB");
const AudioContextClass = window.AudioContext || window.webkitAudioContext;
const audioContext = new AudioContextClass();

const analyserA = audioContext.createAnalyser();
const analyserB = audioContext.createAnalyser();

const sourceA = audioContext.createMediaElementSource(audioA);
const sourceB = audioContext.createMediaElementSource(audioB);

sourceA.connect(analyserA);
sourceB.connect(analyserB);

analyserA.connect(audioContext.destination);
analyserB.connect(audioContext.destination);

analyserA.fftSize = 256;
analyserB.fftSize = 256;
fetch("../api/getPlaylist.php")
    .then(res => res.json())
    .then(songs => {

        if (songs.length === 0) {
            playlist.innerHTML = "<p>No songs uploaded.</p>";
            return;
        }

        playlist.innerHTML = "";

        songs.forEach(song => {

            playlist.innerHTML += `
                <div class="song-item">

                    <strong>${song.title}</strong><br>

                    <small>${song.artist}</small><br><br>

                    <button onclick="loadDeckA('${song.filename}','${song.title}')">
                        Load A
                    </button>

                    <button onclick="loadDeckB('${song.filename}','${song.title}')">
                        Load B
                    </button>

                    <hr>

                </div>
            `;

        });

    });
    const vuA = document.getElementById("vuA");
const vuB = document.getElementById("vuB");

function updateVUMeter(analyser, meter){

    const bufferLength = analyser.frequencyBinCount;
    const data = new Uint8Array(bufferLength);

    analyser.getByteFrequencyData(data);

    let sum = 0;

    for(let i = 0; i < bufferLength; i++){
        sum += data[i];
    }

    const average = sum / bufferLength;

    meter.style.width = (average / 255) * 100 + "%";

    requestAnimationFrame(() => updateVUMeter(analyser, meter));
}

updateVUMeter(analyserA, vuA);
updateVUMeter(analyserB, vuB);
const canvasA = document.getElementById("waveformA");
const canvasB = document.getElementById("waveformB");

const ctxA = canvasA.getContext("2d");
const ctxB = canvasB.getContext("2d");
function drawWaveform(canvas, ctx, analyser) {

    const bufferLength = analyser.fftSize;
    const dataArray = new Uint8Array(bufferLength);

    function draw() {

        requestAnimationFrame(draw);

        analyser.getByteTimeDomainData(dataArray);

        ctx.fillStyle = "#000";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.lineWidth = 2;
        ctx.strokeStyle = "#00ff88";

        ctx.beginPath();

        const sliceWidth = canvas.width / bufferLength;
        let x = 0;

        for (let i = 0; i < bufferLength; i++) {

            const v = dataArray[i] / 128.0;
            const y = (v * canvas.height) / 2;

            if (i === 0) {

                ctx.moveTo(x, y);

            } else {

                ctx.lineTo(x, y);

            }

            x += sliceWidth;

        }

        ctx.lineTo(canvas.width, canvas.height / 2);

        ctx.stroke();

    }

    draw();

}
drawWaveform(canvasA, ctxA, analyserA);
drawWaveform(canvasB, ctxB, analyserB);