<?php
session_start();

// Include config and get connection
try {
    $conn = require __DIR__ . '/config.php';
} catch (Exception $e) {
    die("System configuration error. Please contact support.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($name) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username can only contain letters, numbers and underscores';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            // Check if username exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username already taken';
            } else {
                // Handle file upload
                $profile_image = null;
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['profile_image'];
                    
                    // Validate image
                    $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
                    $max_size = 2 * 1024 * 1024; // 2MB
                    
                    if (!array_key_exists($file['type'], $allowed_types)) {
                        $error = 'Only JPG, PNG, and GIF images are allowed';
                    } elseif ($file['size'] > $max_size) {
                        $error = 'Image size must be less than 2MB';
                    } else {
                        // Generate unique filename
                        $ext = $allowed_types[$file['type']];
                        $filename = uniqid('img_', true) . '.' . $ext;
                        $target_path = 'uploads/' . $filename;
                        
                        if (move_uploaded_file($file['tmp_name'], $target_path)) {
                            $profile_image = $target_path;
                        } else {
                            $error = 'Failed to upload image';
                        }
                    }
                }
                
                if (empty($error)) {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO users (name, username, password, profile_image) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $username, $hashed_password, $profile_image]);
                    
                    if ($stmt->rowCount() > 0) {
                        $success = 'Account created successfully! You can now login.';
                        // Clear form
                        $_POST = array();
                    } else {
                        $error = 'Failed to create account. Please try again.';
                    }
                }
            }
        } catch(PDOException $e) {
            $error = 'Database error. Please try again.';
            file_put_contents('db_errors.log', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Kitchen - Sign Up</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .header h1 {
            color: #ff6b6b;
            margin: 0;
            font-size: 2.2rem;
        }
        
        .alert {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .alert.error {
            background-color: #fee;
            border: 1px solid #fdd;
            color: #c00;
        }
        
        .alert.success {
            background-color: #efe;
            border: 1px solid #ded;
            color: #090;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        
        input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        input:focus {
            border-color: #ff6b6b;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
        }
        
        button {
            width: 100%;
            padding: 0.8rem;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #ff5252;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        
        .login-link a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
        }
        
        #image-preview {
            margin-top: 1rem;
            text-align: center;
        }
        
        #image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 50%;
            border: 3px solid #ff6b6b;
            object-fit: cover;
        }
        
        .file-input-label {
            display: block;
            padding: 0.8rem;
            background-color: #f5f7fa;
            border: 2px dashed #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-input-label:hover {
            background-color: #e9ecef;
            border-color: #ccc;
        }
        
        #profile_image {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Create Your Account</h1>
            <p>Join Smart Kitchen today</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password (min 8 characters)</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>Profile Image</label>
                <label for="profile_image" class="file-input-label">
                    Choose an image...
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                <div id="image-preview"></div>
            </div>
            
            <button type="submit">Sign Up</button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </form>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const fileLabel = document.querySelector('.file-input-label');
            preview.innerHTML = '';
            
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                fileLabel.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                fileLabel.textContent = 'Choose an image...';
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters!');
                return;
            }
        });
    </script>
</body>
</html>