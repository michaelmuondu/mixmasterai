<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user isn't authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}