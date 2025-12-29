<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['get'])) {
    // Fetch member data for editing
    $id = intval($_GET['get']);
    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? AND role = 'member'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    if ($member) {
        header('Content-Type: application/json');
        echo json_encode($member);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Member not found']);
    }
    exit;
}

if (isset($_GET['delete'])) {
    // Delete member
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'member'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Member deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting member.";
    }

    header("Location: ../admin/members.php");
    exit;
}

if (isset($_GET['approve'])) {
    // Approve pending member
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'member' AND status = 'pending'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Member approved successfully!";
    } else {
        $_SESSION['error'] = "Error approving member.";
    }

    header("Location: ../admin/members.php");
    exit;
}

if (isset($_GET['reject'])) {
    // Reject pending member
    $id = intval($_GET['reject']);
    $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ? AND role = 'member' AND status = 'pending'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Member rejected successfully!";
    } else {
        $_SESSION['error'] = "Error rejecting member.";
    }

    header("Location: ../admin/members.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update') {
        // Update member
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        // Validate input
        if (empty($name) || empty($email)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: ../admin/members.php");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            header("Location: ../admin/members.php");
            exit;
        }

        // Check if email is already taken by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['error'] = "Email is already taken.";
            header("Location: ../admin/members.php");
            exit;
        }

        // Update member
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND role = 'member'");
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Member updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating member.";
        }

        header("Location: ../admin/members.php");
        exit;
    }
}

// If no valid action, redirect back
header("Location: ../admin/members.php");
exit;
?>
