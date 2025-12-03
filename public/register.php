<?php
require_once "../config/config.php";
require_once "../config/db.php";

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Membership - Library Management System</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>📚 Create Your Account</h1>
            <p>Join our library community today</p>
        </div>

        <form method="POST" action="controllers/AuthController.php" class="register-form">
            <input type="hidden" name="action" value="register">

            <?php if ($error): ?>
                <div class="error-message">
                    <span>⚠️</span>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <span>✓</span>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name" autocomplete="name">
                <span class="input-focus"></span>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="email">
                <span class="input-focus"></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Create a strong password" autocomplete="new-password">
                <span class="input-focus"></span>
                <small class="password-hint">Min. 6 characters</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password" autocomplete="new-password">
                <span class="input-focus"></span>
            </div>

            <button type="submit" class="register-btn">Create Membership</button>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php?type=member">Login here</a></p>
            </div>
        </form>

        <div class="register-benefits">
            <h3>Member Benefits</h3>
            <ul>
                <li>📖 Browse 1000+ Books</li>
                <li>⭐ Unlimited Access</li>
                <li>📌 Save Favorites</li>
                <li>🔔 Get Notifications</li>
            </ul>
        </div>
    </div>
</body>
</html>
