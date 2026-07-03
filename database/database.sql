-- MixMaster AI Database Schema

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Songs Table
CREATE TABLE IF NOT EXISTS songs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255),
    genre VARCHAR(100),
    filename VARCHAR(255) NOT NULL UNIQUE,
    file_size INT,
    duration INT DEFAULT 0,
    bpm INT DEFAULT 0,
    key_note VARCHAR(10),
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Playlists Table
CREATE TABLE IF NOT EXISTS playlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Playlist Songs (Junction Table)
CREATE TABLE IF NOT EXISTS playlist_songs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    playlist_id INT NOT NULL,
    song_id INT NOT NULL,
    position INT,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_playlist_song (playlist_id, song_id)
);

-- Play History Table
CREATE TABLE IF NOT EXISTS play_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
);

-- User Settings Table
CREATE TABLE IF NOT EXISTS user_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    theme VARCHAR(50) DEFAULT 'dark',
    auto_mix BOOLEAN DEFAULT 1,
    crossfade_duration INT DEFAULT 5,
    volume INT DEFAULT 100,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Recommended Tracks Table
CREATE TABLE IF NOT EXISTS recommendations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    score DECIMAL(5,2),
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
);
