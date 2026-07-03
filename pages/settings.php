<?php
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");
?>

<div class="container">

    <?php require_once("../includes/sidebar.php"); ?>

    <div class="content">

        <h1>⚙ Settings</h1>

        <div class="settings-container">

            <!-- Player Settings -->
            <div class="settings-card">
                <h2>🎧 Player Settings</h2>
                
                <div class="setting-item">
                    <label>Default Volume</label>
                    <input type="range" id="volumeSetting" min="0" max="100" value="100" class="slider">
                    <span id="volumeDisplay">100%</span>
                </div>

                <div class="setting-item">
                    <label>Crossfade Duration (seconds)</label>
                    <input type="number" id="crossfadeSetting" min="0" max="10" value="5" class="input">
                </div>

                <div class="setting-item">
                    <label>
                        <input type="checkbox" id="autoPlaySetting" checked>
                        Auto-play next song
                    </label>
                </div>

                <div class="setting-item">
                    <label>
                        <input type="checkbox" id="autoMixSetting" checked>
                        Enable Auto DJ mixing
                    </label>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="settings-card">
                <h2>🎨 Display Settings</h2>
                
                <div class="setting-item">
                    <label>Theme</label>
                    <select id="themeSetting">
                        <option value="dark" selected>Dark Mode</option>
                        <option value="light">Light Mode</option>
                        <option value="auto">Auto (System)</option>
                    </select>
                </div>

                <div class="setting-item">
                    <label>
                        <input type="checkbox" id="waveformSetting" checked>
                        Show waveforms
                    </label>
                </div>

                <div class="setting-item">
                    <label>
                        <input type="checkbox" id="vuMeterSetting" checked>
                        Show VU meters
                    </label>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="settings-card">
                <h2>👤 Account Settings</h2>
                
                <div class="setting-item">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></p>
                </div>

                <div class="setting-item">
                    <p><strong>Joined:</strong> <?php echo htmlspecialchars($_SESSION['user_created'] ?? 'N/A'); ?></p>
                </div>

                <button class="btn btn-sm" onclick="changePassword()">Change Password</button>
            </div>

            <!-- Storage & Privacy -->
            <div class="settings-card">
                <h2>💾 Storage & Privacy</h2>
                
                <div class="setting-item">
                    <p>Storage used: <strong id="storageUsed">Loading...</strong></p>
                </div>

                <div class="setting-item">
                    <label>
                        <input type="checkbox" id="privateSetting">
                        Private library (only you can see your songs)
                    </label>
                </div>

                <button class="btn btn-sm btn-danger" onclick="deleteAllData()">Delete All Data</button>
            </div>

            <!-- Save Button -->
            <div class="settings-actions">
                <button class="btn btn-primary" onclick="saveSettings()">💾 Save Settings</button>
                <button class="btn btn-secondary" onclick="resetSettings()">↺ Reset to Defaults</button>
            </div>

        </div>

    </div>

</div>

<script>
// Volume display
document.getElementById('volumeSetting').addEventListener('input', function() {
    document.getElementById('volumeDisplay').textContent = this.value + '%';
    localStorage.setItem('volume', this.value);
});

// Load settings
function loadSettings() {
    // Load from localStorage
    const volume = localStorage.getItem('volume') || 100;
    const crossfade = localStorage.getItem('crossfade') || 5;
    const autoPlay = localStorage.getItem('autoPlay') !== 'false';
    const autoMix = localStorage.getItem('autoMix') !== 'false';
    const theme = localStorage.getItem('theme') || 'dark';
    const waveform = localStorage.getItem('waveform') !== 'false';
    const vuMeter = localStorage.getItem('vuMeter') !== 'false';
    const privateLib = localStorage.getItem('privateLib') === 'true';

    document.getElementById('volumeSetting').value = volume;
    document.getElementById('volumeDisplay').textContent = volume + '%';
    document.getElementById('crossfadeSetting').value = crossfade;
    document.getElementById('autoPlaySetting').checked = autoPlay;
    document.getElementById('autoMixSetting').checked = autoMix;
    document.getElementById('themeSetting').value = theme;
    document.getElementById('waveformSetting').checked = waveform;
    document.getElementById('vuMeterSetting').checked = vuMeter;
    document.getElementById('privateSetting').checked = privateLib;

    // Load storage usage
    loadStorageUsage();
}

function loadStorageUsage() {
    fetch('../api/dashboardStats.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('storageUsed').textContent = data.storage + ' MB';
            }
        })
        .catch(e => console.error('Error loading storage:', e));
}

function saveSettings() {
    localStorage.setItem('volume', document.getElementById('volumeSetting').value);
    localStorage.setItem('crossfade', document.getElementById('crossfadeSetting').value);
    localStorage.setItem('autoPlay', document.getElementById('autoPlaySetting').checked);
    localStorage.setItem('autoMix', document.getElementById('autoMixSetting').checked);
    localStorage.setItem('theme', document.getElementById('themeSetting').value);
    localStorage.setItem('waveform', document.getElementById('waveformSetting').checked);
    localStorage.setItem('vuMeter', document.getElementById('vuMeterSetting').checked);
    localStorage.setItem('privateLib', document.getElementById('privateSetting').checked);

    alert('Settings saved successfully!');
}

function resetSettings() {
    if (confirm('Reset all settings to defaults?')) {
        localStorage.clear();
        loadSettings();
        alert('Settings reset to defaults');
    }
}

function changePassword() {
    alert('Change password feature coming soon');
}

function deleteAllData() {
    if (confirm('Are you sure? This will delete all your songs and playlists!')) {
        if (confirm('This action cannot be undone. Are you absolutely sure?')) {
            alert('Delete all data feature coming soon');
        }
    }
}

// Initial load
loadSettings();
</script>

<?php require_once("../includes/footer.php"); ?>
