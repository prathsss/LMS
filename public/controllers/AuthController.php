<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $requested_role = sanitize($_POST['role'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login.php?error=Please enter a valid email&type=" . ($requested_role ?: 'member'));
        exit;
    }

    if (empty($password)) {
        header("Location: ../login.php?error=Please enter your password&type=" . ($requested_role ?: 'member'));
        exit;
    }

    $sql = "SELECT id, name, password, role, status FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            if ($user['status'] !== 'approved') {
                header("Location: ../login.php?error=Your account is pending approval or has been rejected&type=" . ($requested_role ?: 'member'));
                exit;
            }

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
    $phone = sanitize($_POST['phone']);
    $dob = sanitize($_POST['dob']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);

    if (empty($name) || empty($email) || empty($phone) || empty($dob) || empty($password) || empty($confirm_password)) {
        header("Location: ../register.php?error=All fields are required");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../register.php?error=Invalid email format");
        exit;
    }

$normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^[0-9]{10}$/', $normalizedPhone)) {
        header("Location: ../register.php?error=Phone number must be exactly 10 digits");
        exit;
    }

$dobDate = DateTime::createFromFormat('Y-m-d', $dob);
    $today = new DateTime();
    if (!$dobDate || $dobDate->format('Y-m-d') !== $dob || $dobDate > $today) {
        header("Location: ../register.php?error=Invalid date of birth");
        exit;
    }
    
    // Check minimum age (15 years old)
    $minAge = 15;
    $minDob = new DateTime();
    $minDob->modify("-{$minAge} years");
    if ($dobDate > $minDob) {
        header("Location: ../register.php?error=You must be at least {$minAge} years old to register");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match");
        exit;
    }

if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        header("Location: ../register.php?error=Password must be at least 8 characters and include at least 1 letter and 1 number");
        exit;
    }

    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../register.php?error=Email already registered");
        exit;
    }

    $role = 'member';
    $status = 'pending';

    $sql = "INSERT INTO users (name, email, password, phone, dob, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $email, $password, $normalizedPhone, $dob, $role, $status);

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
