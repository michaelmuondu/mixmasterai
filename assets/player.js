// ================================
// MixMaster AI Player Controller
// ================================

const deckA = window.djMixer.deckA;
const deckB = window.djMixer.deckB;

let playlist = [];
let selectedTrack = null;

// ----------------------
// Load Playlist
// ----------------------

async function loadPlaylist() {

    try {

        const response = await fetch("../api/getPlaylist.php");
        const data = await response.json();

        if (!data.success) {
            document.getElementById("playlist").innerHTML =
                "<p>Unable to load playlist.</p>";
            return;
        }

        playlist = data.songs;

        renderPlaylist();

    } catch (err) {

        console.error(err);

        document.getElementById("playlist").innerHTML =
            "<p>Error loading playlist.</p>";

    }

}

// ----------------------
// Render Playlist
// ----------------------

function renderPlaylist() {

    const container = document.getElementById("playlist");

    container.innerHTML = "";

    playlist.forEach(song => {

        const item = document.createElement("div");

        item.className = "playlist-item";

        item.innerHTML = `
            <strong>${song.title}</strong><br>
            <small>${song.artist ?? "Unknown Artist"}</small>

            <div class="playlist-buttons">

                <button class="loadA">Deck A</button>

                <button class="loadB">Deck B</button>

            </div>
        `;

        item.querySelector(".loadA").onclick = () => {

            loadDeck(deckA, song, "A");

        };

        item.querySelector(".loadB").onclick = () => {

            loadDeck(deckB, song, "B");

        };

        container.appendChild(item);

    });

}

// ----------------------
// Load Track
// ----------------------

function loadDeck(deck, song, side) {

    deck.load(song.file_path);

    document.getElementById("track" + side).innerText =
        song.title;

}

// ----------------------
// Controls
// ----------------------

document.getElementById("playA").onclick = () => deckA.play();

document.getElementById("pauseA").onclick = () => deckA.pause();

document.getElementById("stopA").onclick = () => deckA.stop();

document.getElementById("playB").onclick = () => deckB.play();

document.getElementById("pauseB").onclick = () => deckB.pause();

document.getElementById("stopB").onclick = () => deckB.stop();

// ----------------------
// Volume
// ----------------------

document.getElementById("volumeA").oninput = e => {

    deckA.setVolume(e.target.value);

};

document.getElementById("volumeB").oninput = e => {

    deckB.setVolume(e.target.value);

};

// ----------------------
// Crossfader
// ----------------------

document.getElementById("crossfader").oninput = e => {

    window.djMixer.setCrossfader(

        e.target.value / 100

    );

};

// ----------------------
// Seek
// ----------------------

document.getElementById("seekA").oninput = e => {

    deckA.seek(e.target.value);

};

document.getElementById("seekB").oninput = e => {

    deckB.seek(e.target.value);

};

// ----------------------
// Time Display
// ----------------------

function formatTime(seconds) {

    if (isNaN(seconds)) return "00:00";

    const m = Math.floor(seconds / 60);

    const s = Math.floor(seconds % 60);

    return `${m.toString().padStart(2,"0")}:${s.toString().padStart(2,"0")}`;

}

function updateDeck(deck, side) {

    const current = deck.getCurrentTime();

    const duration = deck.getDuration();

    document.getElementById("current"+side).innerText =
        formatTime(current);

    document.getElementById("duration"+side).innerText =
        formatTime(duration);

    if(duration){

        document.getElementById("seek"+side).value =
            (current/duration)*100;

    }

}

function animate(){

    updateDeck(deckA,"A");

    updateDeck(deckB,"B");

    requestAnimationFrame(animate);

}

animate();
const waveformA = new WaveformRenderer(
    "waveformA",
    deckA.getAnalyser(),
    "vuA"
);

const waveformB = new WaveformRenderer(
    "waveformB",
    deckB.getAnalyser(),
    "vuB"
);

waveformA.draw();
waveformB.draw();

loadPlaylist();
document.getElementById("bass").oninput = e => {

    deckA.setBass(Number(e.target.value));
    deckB.setBass(Number(e.target.value));

};

document.getElementById("mid").oninput = e => {

    deckA.setMid(Number(e.target.value));
    deckB.setMid(Number(e.target.value));

};

document.getElementById("treble").oninput = e => {

    deckA.setTreble(Number(e.target.value));
    deckB.setTreble(Number(e.target.value));

};
document.querySelectorAll(".cue-btn").forEach(button=>{

    button.onclick=()=>{

        const deck=
            button.dataset.deck==="A"
            ? deckA
            : deckB;

        const index=
            Number(button.dataset.cue);

        const created=
            deck.toggleCue(index);

        if(created){

            button.classList.add("active");

        }

    };

});
// ===========================
// Pitch Control
// ===========================

function updatePitch(deck, sliderId, labelId) {

    const slider = document.getElementById(sliderId);

    const label = document.getElementById(labelId);

    slider.addEventListener("input", () => {

        const percent = Number(slider.value);

        label.textContent = percent + "%";

        const rate = 1 + (percent / 100);

        deck.setPlaybackRate(rate);

    });

}

updatePitch(deckA, "pitchA", "pitchValueA");
updatePitch(deckB, "pitchB", "pitchValueB");
// ==========================
// SYNC BUTTON
// ==========================

document.getElementById("syncA").addEventListener("click", () => {
    syncDeck(deckA, deckB);
});

document.getElementById("syncB").addEventListener("click", () => {
    syncDeck(deckB, deckA);
});

function syncDeck(sourceDeck, targetDeck) {

    if (!sourceDeck.getDuration() || !targetDeck.getDuration()) {
        alert("Load tracks into both decks first.");
        return;
    }

    sourceDeck.setPlaybackRate(targetDeck.getPlaybackRate());

    if (targetDeck.isPlaying) {
        sourceDeck.audio.currentTime = targetDeck.audio.currentTime;
    }

    console.log("Deck synchronized.");
}
const controllerA =
    new DeckController(
        window.djMixer.deckA,
        "A"
    );

const controllerB =
    new DeckController(
        window.djMixer.deckB,
        "B"
    );

const playlist =
    new Playlist(
        controllerA,
        controllerB
    );

function animate(){

    controllerA.update();

    controllerB.update();

    requestAnimationFrame(animate);

}

animate();