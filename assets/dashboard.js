fetch("../api/dashboardStats.php")
.then(res => res.json())
.then(data => {

    if (!data.success) return;

    document.getElementById("songsCount").innerText = data.songs;

    document.getElementById("playlistCount").innerText = data.playlists;

    document.getElementById("mixCount").innerText = data.mixes;

    document.getElementById("storageCount").innerText =
        data.storage + " MB";

});