const songsContainer = document.getElementById("songs");
const search = document.getElementById("search");

let songs = [];

fetch("../api/getSongs.php")
    .then(res => res.json())
    .then(data => {
        songs = data;
        displaySongs(songs);
    });

function displaySongs(list) {

    songsContainer.innerHTML = "";

    if (list.length === 0) {
        songsContainer.innerHTML = "<h3>No songs found.</h3>";
        return;
    }

    list.forEach(song => {

        const audioSrc = song.file_path ? song.file_path : `../uploads/songs/${song.filename}`;

        songsContainer.innerHTML += `
            <div class="song-card">

                <h3>${song.title}</h3>

                <p><strong>Artist:</strong> ${song.artist || 'Unknown'}</p>

                <p><strong>Album:</strong> ${song.album || 'N/A'}</p>

                <p><strong>Genre:</strong> ${song.genre || 'N/A'}</p>

                <audio controls>
                    <source src="${audioSrc}">
                    Your browser does not support audio.
                </audio>

                <br><br>

                <button onclick="deleteSong(${song.id})">
                    Delete
                </button>

            </div>
        `;

    });

}

search.addEventListener("keyup", () => {

    const value = search.value.toLowerCase();

    const filtered = songs.filter(song => {

        return (
            song.title.toLowerCase().includes(value) ||
            song.artist.toLowerCase().includes(value) ||
            song.genre.toLowerCase().includes(value)
        );

    });

    displaySongs(filtered);

});

function deleteSong(id) {

    if (!confirm("Delete this song?")) return;

    const form = new FormData();
    form.append("id", id);

    fetch("../api/deleteSong.php", {
        method: "POST",
        body: form
    })
    .then(res => res.json())
    .then(data => {

        alert(data.message);

        if (data.success) {

            songs = songs.filter(song => song.id != id);

            displaySongs(songs);

        }

    });

}