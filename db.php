<?php
// Database configuration
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'smart_kitchen'
];

// Establish connection
$conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>