class WaveformRenderer {

    constructor(canvasId, analyser, vuId) {

        this.canvas = document.getElementById(canvasId);

        this.ctx = this.canvas.getContext("2d");

        this.analyser = analyser;

        this.vu = document.getElementById(vuId);

        this.buffer = new Uint8Array(
            analyser.frequencyBinCount
        );
    }

    draw() {

        requestAnimationFrame(() => this.draw());

        this.analyser.getByteTimeDomainData(this.buffer);

        const ctx = this.ctx;

        const w = this.canvas.width;

        const h = this.canvas.height;

        ctx.fillStyle = "#111";

        ctx.fillRect(0, 0, w, h);

        ctx.lineWidth = 2;

        ctx.strokeStyle = "#00ff99";

        ctx.beginPath();

        let slice = w / this.buffer.length;

        let x = 0;

        let peak = 0;

        for (let i = 0; i < this.buffer.length; i++) {

            const v = this.buffer[i] / 128.0;

            const y = v * h / 2;

            if (i === 0)
                ctx.moveTo(x, y);
            else
                ctx.lineTo(x, y);

            x += slice;

            peak = Math.max(
                peak,
                Math.abs(this.buffer[i] - 128)
            );

        }

        ctx.stroke();

        const level = Math.min(
            100,
            peak * 2
        );

        this.vu.style.height = level + "%";

        if (level > 90)
            this.vu.style.background = "#ff3333";
        else if (level > 70)
            this.vu.style.background = "#ffaa00";
        else
            this.vu.style.background = "#00ff66";

    }

}