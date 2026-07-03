<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<div class="container">

    <?php require_once("../includes/sidebar.php"); ?>

    <div class="content">

        <h1>📃 My Playlists</h1>

        <div class="playlist-actions">
            <button id="newPlaylistBtn" class="btn btn-primary">+ Create New Playlist</button>
        </div>

        <div id="playlistsContainer" class="playlists-grid">
            <!-- Playlists loaded via JS -->
        </div>

        <!-- Create Playlist Modal -->
        <div id="playlistModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Create New Playlist</h2>
                <form id="playlistForm">
                    <input type="text" id="playlistName" placeholder="Playlist Name" required>
                    <textarea id="playlistDesc" placeholder="Description" rows="3"></textarea>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>

    </div>

</div>

<script>
const playlistModal = document.getElementById('playlistModal');
const newPlaylistBtn = document.getElementById('newPlaylistBtn');
const closeBtn = document.querySelector('.close');
const playlistForm = document.getElementById('playlistForm');
const playlistsContainer = document.getElementById('playlistsContainer');

// Load playlists
async function loadPlaylists() {
    try {
        const response = await fetch('../api/saveplaylist.php');
        const data = await response.json();
        
        if (data.success) {
            displayPlaylists(data.playlists);
        }
    } catch (error) {
        console.error('Error loading playlists:', error);
    }
}

function displayPlaylists(playlists) {
    playlistsContainer.innerHTML = '';
    
    if (playlists.length === 0) {
        playlistsContainer.innerHTML = '<p>No playlists yet. Create one to get started!</p>';
        return;
    }
    
    playlists.forEach(playlist => {
        const playlistCard = document.createElement('div');
        playlistCard.className = 'playlist-card';
        playlistCard.innerHTML = `
            <h3>${playlist.name}</h3>
            <p>${playlist.description || 'No description'}</p>
            <p class="song-count">${playlist.song_count} songs</p>
            <div class="playlist-actions">
                <button onclick="editPlaylist(${playlist.id})" class="btn btn-sm">Edit</button>
                <button onclick="deletePlaylist(${playlist.id})" class="btn btn-sm btn-danger">Delete</button>
            </div>
        `;
        playlistsContainer.appendChild(playlistCard);
    });
}

// Create playlist
newPlaylistBtn.addEventListener('click', () => {
    playlistModal.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    playlistModal.style.display = 'none';
});

playlistForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('name', document.getElementById('playlistName').value);
    formData.append('description', document.getElementById('playlistDesc').value);
    
    try {
        const response = await fetch('../api/saveplaylist.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            playlistForm.reset();
            playlistModal.style.display = 'none';
            loadPlaylists();
            alert('Playlist created!');
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error creating playlist:', error);
        alert('Error creating playlist');
    }
});

function deletePlaylist(playlistId) {
    if (confirm('Delete this playlist?')) {
        // Would need a delete endpoint
        alert('Delete functionality coming soon');
    }
}

function editPlaylist(playlistId) {
    // Would need an edit interface
    alert('Edit functionality coming soon');
}

// Initial load
loadPlaylists();
</script>

<?php require_once("../includes/footer.php"); ?>
