<?php
session_start();

// Simple authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// You can add more security checks here if needed
?>