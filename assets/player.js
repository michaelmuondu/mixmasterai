const audioA = document.getElementById("audioA");
const audioB = document.getElementById("audioB");

const playA = document.getElementById("playA");
const pauseA = document.getElementById("pauseA");
const stopA = document.getElementById("stopA");

const playB = document.getElementById("playB");
const pauseB = document.getElementById("pauseB");
const stopB = document.getElementById("stopB");

const volumeA = document.getElementById("volumeA");
const volumeB = document.getElementById("volumeB");

playA.onclick = () => audioA.play();

pauseA.onclick = () => audioA.pause();

stopA.onclick = () => {

    audioA.pause();

    audioA.currentTime = 0;

};

playB.onclick = () => audioB.play();

pauseB.onclick = () => audioB.pause();

stopB.onclick = () => {

    audioB.pause();

    audioB.currentTime = 0;

};

volumeA.oninput = () => {

    audioA.volume = volumeA.value;

};

volumeB.oninput = () => {

    audioB.volume = volumeB.value;

};

document.getElementById("crossfader").oninput = function(){

    const value = this.value / 100;

    audioA.volume = (1 - value) * volumeA.value;

    audioB.volume = value * volumeB.value;

};

document.getElementById("loadA").onclick = function(){

    alert("Music library connection coming next.");

};

document.getElementById("loadB").onclick = function(){

    alert("Music library connection coming next.");

};