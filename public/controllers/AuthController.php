<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $requested_role = sanitize($_POST['role'] ?? '');

    $sql = "SELECT id, name, password, role, status FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            // Check if user status is approved
            if ($user['status'] !== 'approved') {
                header("Location: ../login.php?error=Your account is pending approval or has been rejected&type=" . ($requested_role ?: 'member'));
                exit;
            }

            // Check if user role matches requested role
            if ($requested_role && $user['role'] !== $requested_role) {
                header("Location: ../login.php?error=Invalid credentials for " . $requested_role . " login&type=" . $requested_role);
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../member/dashboard.php");
            }
            exit;
        } else {
            header("Location: ../login.php?error=Invalid password&type=" . ($requested_role ?: 'admin'));
            exit;
        }
    } else {
        header("Location: ../login.php?error=Email not found&type=" . ($requested_role ?: 'admin'));
        exit;
    }
}

if ($action === 'register') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match");
        exit;
    }

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../register.php?error=Email already registered");
        exit;
    }

    // Insert new user with pending status
    $role = 'member';
    $status = 'pending';

    $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $password, $role, $status);

    if ($stmt->execute()) {
        header("Location: ../index.php?success=Registration submitted! Please wait for admin approval.");
        exit;
    } else {
        header("Location: ../register.php?error=Error during registration");
        exit;
    }
}

header("Location: ../index.php");
exit;
?>
