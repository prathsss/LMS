<?php
require_once "../config/config.php";
require_once "../config/db.php";

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: member/dashboard.php");
    }
    exit;
}

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$login_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'admin'; // admin or member
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-portal">
        <div class="portal-header">
            <h1>📚 Library Portal</h1>
            <p>Login to your account</p>
        </div>

        <div class="login-options">
            <button type="button" class="login-btn active" onclick="switchLogin('admin', event)">
                <span class="icon">👨‍💼</span>
                <span>Admin</span>
            </button>
            <button type="button" class="login-btn" onclick="switchLogin('member', event)">
                <span class="icon">👤</span>
                <span>Member</span>
            </button>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Admin Login Form -->
            <form id="adminForm" class="active" method="POST" action="controllers/AuthController.php">
                <h2>Admin Login</h2>
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" value="admin">
                
                <div class="form-group">
                    <label for="admin-email">Email Address</label>
                    <input type="email" id="admin-email" name="email" required placeholder="your.email@library.com">
                    <span class="input-focus"></span>
                </div>
                
                <div class="form-group">
                    <label for="admin-password">Password</label>
                    <input type="password" id="admin-password" name="password" required placeholder="Enter your password">
                    <span class="input-focus"></span>
                </div>
                
                <button type="submit" class="login-submit-btn">Login</button>
            </form>

            <!-- Member Login Form -->
            <form id="memberForm" method="POST" action="controllers/AuthController.php">
                <h2>Member Login</h2>
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" value="member">
                
                <div class="form-group">
                    <label for="member-email">Email Address</label>
                    <input type="email" id="member-email" name="email" required placeholder="your.email@library.com">
                    <span class="input-focus"></span>
                </div>
                
                <div class="form-group">
                    <label for="member-password">Password</label>
                    <input type="password" id="member-password" name="password" required placeholder="Enter your password">
                    <span class="input-focus"></span>
                </div>
                
                <button type="submit" class="login-submit-btn">Login</button>
            </form>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </div>

    <script>
        function switchLogin(type, event) {
            if (event) {
                event.preventDefault();
            }
            
            const adminForm = document.getElementById('adminForm');
            const memberForm = document.getElementById('memberForm');
            const buttons = document.querySelectorAll('.login-btn');

            // Update buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.closest('.login-btn').classList.add('active');

            // Update forms
            if (type === 'admin') {
                adminForm.classList.add('active');
                memberForm.classList.remove('active');
            } else {
                memberForm.classList.add('active');
                adminForm.classList.remove('active');
            }
        }
    </script>
</body>
</html>
