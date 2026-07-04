class AudioDeck {

    constructor(audioElementId, audioContext) {

        this.ctx = audioContext;

        this.audio = document.getElementById(audioElementId);

        this.track = null;

        this.isPlaying = false;
        // Hot Cue memory
        this.cues = new Array(8).fill(null);
        // Audio Source
        this.source = this.ctx.createMediaElementSource(this.audio);

        // ==========================
        // EQ
        // ==========================

        this.lowEQ = this.ctx.createBiquadFilter();
        this.lowEQ.type = "lowshelf";
        this.lowEQ.frequency.value = 320;

        this.midEQ = this.ctx.createBiquadFilter();
        this.midEQ.type = "peaking";
        this.midEQ.frequency.value = 1000;
        this.midEQ.Q.value = 1;

        this.highEQ = this.ctx.createBiquadFilter();
        this.highEQ.type = "highshelf";
        this.highEQ.frequency.value = 3200;

        // Filter Effect

        this.filter = this.ctx.createBiquadFilter();
        this.filter.type = "lowpass";
        this.filter.frequency.value = 20000;

        // Gain

        this.gainNode = this.ctx.createGain();
        this.gainNode.gain.value = 1;

        // Analyser

        this.analyser = this.ctx.createAnalyser();
        this.analyser.fftSize = 2048;

        // Signal Chain

        this.source
            .connect(this.lowEQ)
            .connect(this.midEQ)
            .connect(this.highEQ)
            .connect(this.filter)
            .connect(this.gainNode)
            .connect(this.analyser)
            .connect(this.ctx.destination);

    }

    load(src) {

        this.track = src;

        this.audio.src = src;

        this.audio.load();

    }

    async play() {

        await this.ctx.resume();

        await this.audio.play();

        this.isPlaying = true;

    }

    pause() {

        this.audio.pause();

        this.isPlaying = false;

    }

    stop() {

        this.audio.pause();

        this.audio.currentTime = 0;

        this.isPlaying = false;

    }

    // ==========================
    // Mixer
    // ==========================

    setVolume(v) {

        this.gainNode.gain.value = Number(v);

    }

    setPlaybackRate(rate){

    rate = Math.max(0.5, Math.min(2, rate));

    this.audio.playbackRate = rate;

    this.playbackRate = rate;

}

    seek(percent) {

        if (!this.audio.duration) return;

        this.audio.currentTime =
            (percent / 100) * this.audio.duration;

    }

    // ==========================
    // EQ
    // ==========================

    setBass(v) {

        this.lowEQ.gain.value = Number(v);

    }

    setMid(v) {

        this.midEQ.gain.value = Number(v);

    }

    setTreble(v) {

        this.highEQ.gain.value = Number(v);

    }

    // ==========================
    // Filter
    // ==========================

    setFilter(freq) {

        this.filter.frequency.value = Number(freq);

    }

    // ==========================
    // Getters
    // ==========================

    getCurrentTime() {

        return this.audio.currentTime;

    }

    getDuration() {

        return this.audio.duration || 0;

    }

    getAnalyser() {

        return this.analyser;

    }
    getPlaybackRate() {

    return this.audio.playbackRate;

}
setCue(index) {

    this.cues[index] = this.audio.currentTime;

}

jumpCue(index) {

    if (this.cues[index] !== null) {

        this.audio.currentTime = this.cues[index];

    }

}

toggleCue(index) {

    if (this.cues[index] === null) {

        this.setCue(index);

        return true;

    }

    this.jumpCue(index);

    return false;

}
}

class DJMixer {

    constructor() {

        this.ctx = new (window.AudioContext || window.webkitAudioContext)();

        this.deckA = new AudioDeck("audioA", this.ctx);

        this.deckB = new AudioDeck("audioB", this.ctx);

        this.crossValue = 0.5;

    }

    setCrossfader(value) {

        this.crossValue = Number(value);

        // Equal-power fade

        const leftGain = Math.cos(this.crossValue * Math.PI / 2);
        const rightGain = Math.sin(this.crossValue * Math.PI / 2);

        this.deckA.setVolume(leftGain);

        this.deckB.setVolume(rightGain);

    }

}

window.djMixer = new DJMixer();