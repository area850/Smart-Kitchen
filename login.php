<?php
session_start();
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, username, name, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuova Cafe - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5a2c0c;
            --secondary: #c49b63;
            --accent: #e8c07d;
            --light: #fff8f0;
            --dark: #2a1806;
        }
        
        body {
            background: linear-gradient(135deg, var(--light) 0%, #f5e6d2 100%);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        
        .floating-coffee {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
            animation: float 15s infinite ease-in-out;
        }
        
        .floating-coffee:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 100px;
            animation-delay: 0s;
        }
        
        .floating-coffee:nth-child(2) {
            top: 70%;
            left: 80%;
            width: 150px;
            animation-delay: 2s;
        }
        
        .floating-coffee:nth-child(3) {
            top: 30%;
            left: 75%;
            width: 80px;
            animation-delay: 4s;
        }
        
        .floating-coffee:nth-child(4) {
            top: 80%;
            left: 15%;
            width: 120px;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            transform: translateY(0);
            transition: all 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease;
        }
        
        .login-header img {
            height: 100px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 5px 5px rgba(0, 0, 0, 0.1));
            transition: all 0.5s ease;
        }
        
        .login-header img:hover {
            transform: scale(1.05) rotate(-5deg);
        }
        
        .login-header h2 {
            color: var(--primary);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        
        .login-header p {
            color: var(--secondary);
            font-weight: 500;
        }
        
        .form-control {
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(200, 160, 100, 0.25);
            background: white;
        }
        
        .password-input-group {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
            z-index: 5;
            transition: all 0.3s ease;
        }
        
        .toggle-password:hover {
            color: var(--primary);
            transform: translateY(-50%) scale(1.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(90, 44, 12, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(90, 44, 12, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        
        .btn-login:hover::after {
            transform: translateX(100%);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
            animation: fadeInUp 0.8s ease;
        }
        
        .login-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            position: relative;
        }
        
        .login-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        
        .login-footer a:hover::after {
            width: 100%;
        }
        
        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
            text-align: center;
            background: rgba(220, 53, 69, 0.1);
            padding: 0.75rem;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            animation: shake 0.5s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            20%, 60% {
                transform: translateX(-5px);
            }
            40%, 80% {
                transform: translateX(5px);
            }
        }
        
        /* Floating animation for form */
        @keyframes floating {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        .login-container {
            animation: floating 6s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Floating coffee bean decorations -->
    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936886.png" class="floating-coffee">
    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936886.png" class="floating-coffee">
    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936886.png" class="floating-coffee">
    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936886.png" class="floating-coffee">
    
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="https://cdn-icons-png.flaticon.com/512/686/686458.png" alt="Neuova Cafe Logo">
                <h2>Welcome Back</h2>
                <p>Sign in to your dashboard</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <i class="toggle-password fas fa-eye" onclick="togglePassword('password')"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>
            
            <div class="login-footer">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.querySelector(`#${fieldId} + .toggle-password`);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Add ripple effect to login button
        document.querySelector('.btn-login').addEventListener('click', function(e) {
            const btn = e.currentTarget;
            const x = e.clientX - btn.getBoundingClientRect().left;
            const y = e.clientY - btn.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            btn.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 500);
        });
    </script>
</body>
</html>