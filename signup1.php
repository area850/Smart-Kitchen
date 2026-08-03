
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuova Cafe - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5a2c0c;
            --secondary: #c49b63;
            --accent: #e8c07d;
            --light: #fff8f0;
            --dark: #2a1806;
            --cream: #f9f1e6;
        }
        
        body {
            background: url('https://images.unsplash.com/photo-1445116572660-236099ec97a0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        
     
        
        .coffee-steam {
            position: absolute;
            width: 100px;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,255,255,0.3)" d="M20,20 Q30,5 40,20 Q50,5 60,20 Q70,5 80,20" stroke="none" stroke-width="2"/></svg>') no-repeat;
            animation: steam 8s infinite ease-in-out;
            opacity: 0.5;
            z-index: 0;
        }
        
        @keyframes steam {
            0%, 100% {
                transform: translateY(0) scale(1);
                opacity: 0.2;
            }
            50% {
                transform: translateY(-50px) scale(1.2);
                opacity: 0.5;
            }
        }
        
        .signup-container {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem;
            background: rgba(255, 248, 240, 0.9);
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            transform: translateY(0);
            transition: all 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            animation: floating 8s ease-in-out infinite;
            overflow: hidden;
        }
        
        .signup-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(232,192,125,0.1) 0%, rgba(255,255,255,0) 70%);
            animation: rotate 20s linear infinite;
            z-index: -1;
        }
        
        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        
        .signup-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease;
        }
        
        .signup-header img {
            height: 80px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 5px 5px rgba(0, 0, 0, 0.2));
            transition: all 0.5s ease;
        }
        
        .signup-header img:hover {
            transform: scale(1.1) rotate(-5deg);
        }
        
        .signup-header h2 {
            color: var(--primary);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            font-family: 'Playfair Display', serif;
            position: relative;
            display: inline-block;
        }
        
        .signup-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent);
            border-radius: 3px;
        }
        
        .signup-header p {
            color: var(--secondary);
            font-weight: 500;
            font-size: 1.1rem;
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
        
        .btn-signup {
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
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        
        .btn-signup:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(90, 44, 12, 0.4);
        }
        
        .btn-signup:active {
            transform: translateY(0);
        }
        
        .btn-signup::after {
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
        
        .btn-signup:hover::after {
            transform: translateX(100%);
        }
        
        .signup-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--primary);
            animation: fadeInUp 0.8s ease;
        }
        
        .signup-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            position: relative;
        }
        
        .signup-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        
        .signup-footer a:hover::after {
            width: 100%;
        }
        
        .error-message {
            color: #dc3545;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 12px;
            border-left: 4px solid #dc3545;
            animation: shake 0.5s ease;
        }
        
        .error-message ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        
        .password-strength {
            height: 5px;
            background-color: #e9ecef;
            margin-top: 5px;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.5s ease;
        }
        
        .profile-image-container {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .profile-image-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--cream);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background-color: var(--cream);
            display: inline-block;
            position: relative;
            overflow: hidden;
        }
        
        .profile-image-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .profile-image-preview::before {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: var(--secondary);
            opacity: 0.5;
        }
        
        .profile-image-preview.has-image::before {
            content: none;
        }
        
        .profile-image-upload {
            display: none;
        }
        
        .profile-image-label {
            position: absolute;
            bottom: 0;
            right: calc(50% - 60px);
            background: var(--accent);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            color: var(--primary);
        }
        .input-box {
    width: 100%;
    margin: 10px 0;
}

.input-box select.department {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
    font-size: 15px;
    font-weight: 500;
    color: #333;
    outline: none;
    appearance: none; /* Remove default arrow */
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
}

.input-box select.department:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.1);
}

.input-box select.department:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
}

        
        .profile-image-label:hover {
            transform: scale(1.1);
            background: var(--secondary);
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
        
        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Coffee cup animation */
        .coffee-cup {
            position: absolute;
            width: 100px;
            height: 100px;
            background: url('https://cdn-icons-png.flaticon.com/512/2936/2936886.png') no-repeat center center;
            background-size: contain;
            animation: float 6s ease-in-out infinite;
            opacity: 0.8;
            z-index: 0;
        }
        
        .coffee-cup:nth-child(1) {
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .coffee-cup:nth-child(2) {
            top: 70%;
            left: 85%;
            animation-delay: 2s;
        }
        
        .coffee-cup:nth-child(3) {
            top: 30%;
            left: 80%;
            animation-delay: 4s;
        }
    </style>
</head>
<body>
    <!-- Coffee steam animations -->
    <div class="coffee-steam" style="top: 10%; left: 15%; animation-delay: 0s;"></div>
    <div class="coffee-steam" style="top: 20%; left: 80%; animation-delay: 2s;"></div>
    <div class="coffee-steam" style="top: 70%; left: 20%; animation-delay: 4s;"></div>
    
    <!-- Floating coffee cups -->
    <div class="coffee-cup"></div>
    <div class="coffee-cup"></div>
    <div class="coffee-cup"></div>
    
    <div class="container">
        <div class="signup-container">
            <div class="signup-header">
                <img src="https://cdn-icons-png.flaticon.com/512/686/686458.png" alt="Neuova Cafe Logo">
                <h2>Join Neuova Cafe</h2>
                <p>Create your account to get started</p>
            </div>
            
            
            
            <form method="POST" action="sign_up.php" id="signupForm" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                    <small class="text-muted">Letters, numbers, and underscores only</small>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <i class="toggle-password fas fa-eye" onclick="togglePassword('password')"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <i class="toggle-password fas fa-eye" onclick="togglePassword('confirm_password')"></i>
                    </div>
                     <div class="input-box">
                <select class="department" name="department">
                    <option value="">Select a department</option>
                    <option value="casher1">Casher 1</option>
                    <option value="casher2">Casher 2</option>
                    <option value="order">Order</option>
                    <option value="barrista">Barrista</option>
                </select>
            </div>
      
                </div>
                
                <button type="submit" class="btn btn-signup">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </form>
            
            <div class="signup-footer">
                Already have an account? <a href="login.php">Login here</a>
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
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 3;
            if (password.length >= 12) strength += 1;
            
            // Check for mixed case
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Check for numbers
            if (/\d/.test(password)) strength += 1;
            
            // Check for special chars
            if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
            
            // Update strength bar
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
            const width = (strength / 5) * 100;
            
            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = colors[strength - 1] || '#dc3545';
        });
        
        // Profile image preview
        document.getElementById('profileImageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('profileImagePreview');
                    preview.src = event.target.result;
                    preview.classList.add('has-image');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters');
                return false;
            }
            
            return true;
        });
        
        // Add ripple effect to signup button
        document.querySelector('.btn-signup').addEventListener('click', function(e) {
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