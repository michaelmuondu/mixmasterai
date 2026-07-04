class DeckController {

    constructor(deck, side) {

        this.deck = deck;
        this.side = side;

        // Audio Information
        this.track = document.getElementById("track" + side);
        this.current = document.getElementById("current" + side);
        this.duration = document.getElementById("duration" + side);

        // Buttons
        this.play = document.getElementById("play" + side);
        this.pause = document.getElementById("pause" + side);
        this.stop = document.getElementById("stop" + side);
        this.load = document.getElementById("load" + side);

        // Sliders
        this.volume = document.getElementById("volume" + side);
        this.pitch = document.getElementById("pitch" + side);
        this.seek = document.getElementById("seek" + side);

        this.pitchLabel =
            document.getElementById("pitchValue" + side);

        this.bindEvents();

    }

    bindEvents() {

        this.play.onclick = () => {

            this.deck.play();

        };

        this.pause.onclick = () => {

            this.deck.pause();

        };

        this.stop.onclick = () => {

            this.deck.stop();

        };

        this.volume.oninput = e => {

            this.deck.setVolume(e.target.value);

        };

        this.pitch.oninput = e => {

            const percent = Number(e.target.value);

            this.pitchLabel.innerHTML =
                percent + "%";

            this.deck.setPlaybackRate(
                1 + percent / 100
            );

        };

        this.seek.oninput = e => {

            this.deck.seek(e.target.value);

        };

    }

    loadTrack(song) {

        this.deck.load(song.file_path);

        this.track.innerHTML =
            song.title;

    }

    update() {

        const current =
            this.deck.getCurrentTime();

        const duration =
            this.deck.getDuration();

        this.current.innerHTML =
            this.format(current);

        this.duration.innerHTML =
            this.format(duration);

        if (duration > 0) {

            this.seek.value =
                current / duration * 100;

        }

    }

    format(sec) {

        if (isNaN(sec))
            return "00:00";

        const m =
            Math.floor(sec / 60);

        const s =
            Math.floor(sec % 60);

        return String(m).padStart(2, "0")
            + ":" +
            String(s).padStart(2, "0");

    }

}