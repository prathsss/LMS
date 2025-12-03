<?php
// Start session for login system
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL of your project
define("BASE_URL", "http://localhost/Library-management-system/");

// Project name
define("PROJECT_NAME", "Library Management System");

// Default timezone
date_default_timezone_set("Asia/Kathmandu");

// Admin email (optional)
define("ADMIN_EMAIL", "admin@library.com");

// Debug mode (true = show errors, false = hide errors)
define("DEBUG_MODE", true);

// Error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>

