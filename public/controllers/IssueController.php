<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Approve issue request
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);

    // Verify request exists and is requested
    $res = $conn->query("SELECT * FROM book_issues WHERE id = $id AND status = 'requested'");
    $req = $res ? $res->fetch_assoc() : null;

    if (!$req) {
        $_SESSION['error'] = "Issue request not found or already processed.";
        header("Location: ../admin/issues.php");
        exit;
    }

    $book_id = intval($req['book_id']);

    // Check available quantity
    $bk = $conn->query("SELECT available_quantity FROM books WHERE id = $book_id");
    $book = $bk ? $bk->fetch_assoc() : null;

    if (!$book || $book['available_quantity'] <= 0) {
        $_SESSION['error'] = "Cannot approve: book out of stock.";
        header("Location: ../admin/issues.php");
        exit;
    }

    // Approve: set status issued, set issue_date and due_date, decrement book
    $due_date = date('Y-m-d', strtotime('+14 days'));
    $due_date_esc = $conn->real_escape_string($due_date);

    $conn->query("UPDATE book_issues SET status='issued', issue_date=NOW(), due_date='$due_date_esc' WHERE id = $id");
    $conn->query("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = $book_id");

    // Log approval
    $admin_id = intval($_SESSION['user_id']);
    $log = $conn->real_escape_string("Admin approved issue ID $id for book ID $book_id");
    $conn->query("INSERT INTO logs (user_id, action, details, created_at) VALUES ($admin_id, 'Issue Approved', '$log', NOW())");

    $_SESSION['success'] = "Issue request approved and book issued.";
    header("Location: ../admin/issues.php");
    exit;
}

// Reject issue request
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);

    // Verify request exists and is requested
    $res = $conn->query("SELECT * FROM book_issues WHERE id = $id AND status = 'requested'");
    $req = $res ? $res->fetch_assoc() : null;

    if (!$req) {
        $_SESSION['error'] = "Issue request not found or already processed.";
        header("Location: ../admin/issues.php");
        exit;
    }

    // Mark request as rejected
    $conn->query("UPDATE book_issues SET status='rejected' WHERE id = $id");

    // Log rejection
    $admin_id = intval($_SESSION['user_id']);
    $log = $conn->real_escape_string("Admin rejected issue ID $id for book ID {$req['book_id']}");
    $conn->query("INSERT INTO logs (user_id, action, details, created_at) VALUES ($admin_id, 'Issue Rejected', '$log', NOW())");

    $_SESSION['success'] = "Issue request rejected.";
    header("Location: ../admin/issues.php");
    exit;
}

// No valid action
header("Location: ../admin/issues.php");
exit;
?>
