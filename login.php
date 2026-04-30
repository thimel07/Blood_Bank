<?php
/**
 * Login Page
 * Blood Bank Management System
 */

require_once 'includes/db_connect.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: /blood-bank/admin/dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            // Query user from database
            $stmt = $conn->prepare("SELECT id, username, password, full_name, role, status FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Check if account is active
                if ($user['status'] !== 'active') {
                    $error_message = 'Your account has been deactivated. Please contact administrator.';
                } elseif (verifyPassword($password, $user['password'])) {
                    // Password is correct, create session
                    startSecureSession();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    // Log audit
                    logAudit('User Login', 'admin_user', $user['id']);

                    // Redirect to dashboard or previous page
                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/blood-bank/admin/dashboard.php';
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    // Debug: Check hash format
                    $hashDebug = 'Password hash validation failed. ';
                    if (strpos($user['password'], '$2y$') === 0 || strpos($user['password'], '$2a$') === 0) {
                        $hashDebug .= 'This appears to be a bcrypt hash but verification failed.';
                    } elseif (strlen($user['password']) > 64) {
                        $hashDebug .= 'Hash format unrecognized.';
                    }
                    $error_message = 'Invalid password. Please try again.';
                    // Log for debugging
                    error_log('Login attempt failed for user: ' . $username . ' - ' . $hashDebug);
                }
            } else {
                $error_message = 'Username not found. Please try again.';
            }

            $stmt->close();
        } catch (Exception $e) {
            $error_message = 'An error occurred during login. Please try again.';
            error_log($e->getMessage());
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Bank Management System</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #e63946;
            --dark-color: #2d3436;
            --light-color: #f8f9fa;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Navigation Bar */
        .navbar-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .navbar-brand {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand:hover {
            color: #ffd700;
        }

        .home-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
        }

        .home-btn:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
            animation: float 6s ease-in-out infinite;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -50px;
            left: 10%;
            animation: float 8s ease-in-out infinite reverse;
            z-index: -1;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-30px); }
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            animation: slideInUp 0.6s ease;
            max-height: 90vh;
            overflow-y: auto;
            margin-top: 80px;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #999;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.7rem;
            color: var(--dark-color);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1.2rem;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
            background: white;
        }

        .form-control::placeholder {
            color: #ccc;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-btn {
            position: absolute;
            right: 1.2rem;
            top: 2.6rem;
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            padding: 0.3rem;
            width: 2rem;
            height: 2rem;
            z-index: 10;
            pointer-events: auto;
        }

        .password-toggle .toggle-btn:hover {
            color: var(--primary-color);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-forgot a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
            position: relative;
            z-index: 5;
            pointer-events: auto;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(230, 57, 70, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
            animation: slideInDown 0.4s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: rgba(239, 71, 111, 0.1);
            color: #ef476f;
            border: 1px solid #ef476f;
        }

        .alert-success {
            background: rgba(6, 214, 160, 0.1);
            color: #06a070;
            border: 1px solid #06d6a0;
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            color: #aaa;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            display: block;
            height: 1px;
            background: #ddd;
            margin-bottom: 1rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.9rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .register-section {
            text-align: center;
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(230, 57, 70, 0.05);
            border-radius: 10px;
        }

        .register-section p {
            margin: 0 0 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
        }

        .register-btn {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color) 0%, #c1121f 100%);
            color: white;
            padding: 0.7rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
        }

        .register-btn:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
        }

        /* Loading spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            display: none;
            margin-right: 0.5rem;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-btn.loading .spinner {
            display: inline-block;
        }

        .login-btn.loading span {
            opacity: 0;
        }



        @media (max-width: 480px) {
            .login-box {
                padding: 2rem 1.5rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .login-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar-top">
    <a href="/blood-bank/" class="navbar-brand">
        <i class="fas fa-hospital"></i>
        Blood Bank System
    </a>
    <a href="/blood-bank/" class="home-btn">
        <i class="fas fa-home"></i> Home
    </a>
</div>

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-droplet"></i>
            </div>
            <h1 class="login-title">Blood Bank System</h1>
            <p class="login-subtitle">Admin Login Portal</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       placeholder="Enter your username" required autofocus>
            </div>

            <div class="form-group password-toggle">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Enter your password" required>
                <button type="button" class="toggle-btn" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <div class="remember-forgot">
                <label class="form-check-label mb-0">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    Remember me
                </label>
                <a href="#" onclick="alert('Contact administrator to reset password'); return false;">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <span class="spinner"></span>
                <span>Sign In</span>
            </button>
        </form>

        <div class="divider">OR</div>

        <div class="login-footer">
            Need help? <a href="final_admin_fix.php" style="color: var(--primary-color); font-weight: 600;">Click here to fix login issues</a> | <a href="mailto:support@bloodbank.com">Contact Support</a>
        </div>

        <div class="register-section">
            <p>New member? Created a donor account today!</p>
            <a href="/blood-bank/#donors" class="register-btn">
                <i class="fas fa-user-plus"></i> Register as Donor
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.querySelector('.toggle-btn');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            passwordInput.type = 'password';
            toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }

    // Form submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const loginBtn = document.getElementById('loginBtn');
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all fields');
            return;
        }

        loginBtn.classList.add('loading');
        loginBtn.disabled = true;
    });

    // Remember me functionality
    document.getElementById('loginForm').addEventListener('load', function() {
        const rememberCheckbox = document.getElementById('remember');
        const usernameInput = document.getElementById('username');
        
        const savedUsername = localStorage.getItem('rememberedUsername');
        if (savedUsername) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }
    });

    document.getElementById('loginForm').addEventListener('submit', function() {
        const rememberCheckbox = document.getElementById('remember');
        const usernameInput = document.getElementById('username');
        
        if (rememberCheckbox.checked) {
            localStorage.setItem('rememberedUsername', usernameInput.value);
        } else {
            localStorage.removeItem('rememberedUsername');
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && (e.target.type === 'text' || e.target.type === 'password')) {
            document.getElementById('loginForm').submit();
        }
    });

    // Load remembered username on page load
    window.addEventListener('DOMContentLoaded', function() {
        const savedUsername = localStorage.getItem('rememberedUsername');
        if (savedUsername) {
            document.getElementById('username').value = savedUsername;
            document.getElementById('remember').checked = true;
        }
    });
</script>

</body>
</html>
