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

// ===== LOAD FUNCTIONS =====
function loadDeckA(filename, title) {
    audioA.src = `../uploads/songs/${filename}`;
    trackA.textContent = title;
    trackA.style.color = '#00ff88';
    
    // Add active deck effect
    document.querySelector('.deck:nth-child(1)').classList.add('active');
    setTimeout(() => {
        document.querySelector('.deck:nth-child(1)').classList.remove('active');
    }, 2000);
}

function loadDeckB(filename, title) {
    audioB.src = `../uploads/songs/${filename}`;
    trackB.textContent = title;
    trackB.style.color = '#00ff88';
    
    // Add active deck effect
    document.querySelector('.deck:nth-child(3)').classList.add('active');
    setTimeout(() => {
        document.querySelector('.deck:nth-child(3)').classList.remove('active');
    }, 2000);
}

// ===== TIME UPDATE HANDLERS =====
audioA.addEventListener('timeupdate', () => {
    document.getElementById('currentA').textContent = formatTime(audioA.currentTime);
    seekA.value = (audioA.currentTime / audioA.duration) * 100 || 0;
});

audioB.addEventListener('timeupdate', () => {
    document.getElementById('currentB').textContent = formatTime(audioB.currentTime);
    seekB.value = (audioB.currentTime / audioB.duration) * 100 || 0;
});

audioA.addEventListener('loadedmetadata', () => {
    document.getElementById('durationA').textContent = formatTime(audioA.duration);
});

audioB.addEventListener('loadedmetadata', () => {
    document.getElementById('durationB').textContent = formatTime(audioB.duration);
});

// ===== SEEK BAR HANDLERS =====
seekA.addEventListener('change', () => {
    audioA.currentTime = (seekA.value / 100) * audioA.duration;
});

seekB.addEventListener('change', () => {
    audioB.currentTime = (seekB.value / 100) * audioB.duration;
});

// ===== VOLUME HANDLERS =====
volumeA.addEventListener('input', () => {
    audioA.volume = volumeA.value;
});

volumeB.addEventListener('input', () => {
    audioB.volume = volumeB.value;
});

// ===== CROSSFADER =====
crossfader.addEventListener('input', () => {
    const value = crossfader.value / 100;
    audioA.volume = 1 - value;
    audioB.volume = value;
});

// ===== UTILITY FUNCTION =====
function formatTime(seconds) {
    if (!seconds || isNaN(seconds)) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// ===== BUTTON ANIMATIONS =====
document.querySelectorAll('.controls button').forEach(button => {
    button.addEventListener('click', function() {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 150);
    });
});

// ===== RESPONSIVE DECK STYLING =====
window.addEventListener('resize', () => {
    const width = window.innerWidth;
    if (width < 1000) {
        document.querySelector('.dj-console').style.gridTemplateColumns = '1fr';
    } else {
        document.querySelector('.dj-console').style.gridTemplateColumns = '1fr 200px 1fr';
    }
});