<?php
session_start(); // Start session for session management

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $_SESSION = [];

    // Destroy the session
    session_destroy();
    
    header('Location: ../login.html');
    exit();
} else {
    header('Location: ../login.html');
    exit();
}
?>
