class DeckController {

    constructor(deck, side) {

        this.deck = deck;
        this.side = side;

        this.track = document.getElementById("track" + side);

        this.playBtn = document.getElementById("play" + side);
        this.pauseBtn = document.getElementById("pause" + side);
        this.stopBtn = document.getElementById("stop" + side);

        this.volume = document.getElementById("volume" + side);

        this.seek = document.getElementById("seek" + side);

        this.pitch = document.getElementById("pitch" + side);

        this.pitchLabel =
            document.getElementById("pitchValue" + side);

        this.current =
            document.getElementById("current" + side);

        this.duration =
            document.getElementById("duration" + side);

        this.bindEvents();

    }

    bindEvents() {

        this.playBtn.onclick = () => this.deck.play();

        this.pauseBtn.onclick = () => this.deck.pause();

        this.stopBtn.onclick = () => this.deck.stop();

        this.volume.oninput = e => {

            this.deck.setVolume(e.target.value);

        };

        this.seek.oninput = e => {

            this.deck.seek(e.target.value);

        };

        this.pitch.oninput = e => {

            const percent = Number(e.target.value);

            this.pitchLabel.innerText = percent + "%";

            this.deck.setPlaybackRate(
                1 + percent / 100
            );

        };

    }

    load(song) {

        this.deck.load(song.file_path);

        this.track.innerText = song.title;

    }

    update() {

        const current =
            this.deck.getCurrentTime();

        const duration =
            this.deck.getDuration();

        this.current.innerText =
            this.format(current);

        this.duration.innerText =
            this.format(duration);

        if (duration) {

            this.seek.value =
                current / duration * 100;

        }

    }

    format(sec) {

        if (isNaN(sec))
            return "00:00";

        const m = Math.floor(sec / 60);

        const s = Math.floor(sec % 60);

        return `${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;

    }

}