<?php
// Database connection

$conn = new mysqli("192.168.1.200", "root", "root_password", "smart_kitchen");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $department= $_POST['department']; // Plain text password (not recommended)
    
   
    // Validate inputs
    if (empty($username) || empty($password) || empty($depatment)) {
        die("Error: All fields are required");
    }

    // Insert data with registration datetime
    $sql = "INSERT INTO users (username, department, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $username, $password, $department);
    
    if ($stmt->execute()) {
        header("Location: login.html?signup=success");
        exit();
    } else {
        if ($conn->errno == 1062) {
            die("Error: Username already exists");
        } else {
            die("Error: " . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: signup.php");
    exit();
}
?>