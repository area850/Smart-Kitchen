<?php
// PHP Code
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login final.php");
    exit();
}

// Database configuration
$host = "localhost";
$dbname = "smart_kitchen";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuova Cafe - Dashboard</title>
    <style>
        /* CSS Code */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #6d4c41;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .profile-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        
        .logout-btn {
            background-color: white;
            color: #6d4c41;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }
        
        .logout-btn:hover {
            background-color: #f0f0f0;
        }
        
        main {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #6d4c41;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="user-info">
                <?php if ($user['profile_image']): ?>
                    <img src="<?php echo $user['profile_image']; ?>" alt="Profile Image" class="profile-image">
                <?php else: ?>
                    <div class="profile-image" style="background-color: #bcaaa4; display: flex; justify-content: center; align-items: center;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h2><?php echo $user['name']; ?></h2>
                    <p>@<?php echo $user['username']; ?></p>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        
        <main>
            <h1 class="welcome-message">Welcome to Neuova Cafe Dashboard</h1>
            <p>This is your personalized dashboard. More features coming soon!</p>
        </main>
    </div>
</body>
</html>