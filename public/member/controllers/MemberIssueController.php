<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/config.php';

$member_id = $_SESSION['user_id'];


// Issue Book

if (isset($_GET['issue'])) {
    $book_id = intval($_GET['issue']);

    // Check if already issued & not returned
    $check = $conn->prepare("SELECT * FROM book_issues WHERE member_id=? AND book_id=? AND status='issued'");
    $check->bind_param("ii", $member_id, $book_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "You have already issued this book and haven't returned it yet.";
        header("Location: ../books.php");
        exit;
    }

    // Check available quantity
    $qty = $conn->prepare("SELECT available_quantity FROM books WHERE id=?");
    $qty->bind_param("i", $book_id);
    $qty->execute();
    $book = $qty->get_result()->fetch_assoc();

    if ($book['available_quantity'] <= 0) {
        $_SESSION['error'] = "This book is currently out of stock.";
        header("Location: ../books.php");
        exit;
    }

    // Calculate due date (14 days from now)
    $due_date = date('Y-m-d', strtotime('+14 days'));

    // Issue the book
    $issue_stmt = $conn->prepare("INSERT INTO book_issues (member_id, book_id, issue_date, due_date, status) VALUES (?, ?, NOW(), ?, 'issued')");
    $issue_stmt->bind_param("iis", $member_id, $book_id, $due_date);
    $issue_stmt->execute();

    // Decrease available quantity
    $update_stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id=?");
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    // Log the issue action
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details, created_at) VALUES (?, 'Book Issued', ?, NOW())");
    $log_details = "Member issued book ID $book_id";
    $log_stmt->bind_param("is", $member_id, $log_details);
    $log_stmt->execute();

    $_SESSION['success'] = "Book issued successfully! Due date: " . $due_date;
    header("Location: ../books.php");
    exit;
}


// Return Book

if (isset($_GET['return'])) {
    $issue_id = intval($_GET['return']);

    // Get book id and verify ownership
    $row_stmt = $conn->prepare("SELECT book_id FROM book_issues WHERE id=? AND member_id=? AND status='issued'");
    $row_stmt->bind_param("ii", $issue_id, $member_id);
    $row_stmt->execute();
    $result = $row_stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Invalid return request.";
        header("Location: ../my_issues.php");
        exit;
    }

    $row = $result->fetch_assoc();
    $book_id = $row['book_id'];

    // Update issue status
    $return_stmt = $conn->prepare("UPDATE book_issues SET status='returned', return_date=NOW() WHERE id=?");
    $return_stmt->bind_param("i", $issue_id);
    $return_stmt->execute();

    // Increase available quantity
    $update_stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id=?");
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    // Log the return action
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details, created_at) VALUES (?, 'Book Returned', ?, NOW())");
    $log_details = "Member returned book ID $book_id";
    $log_stmt->bind_param("is", $member_id, $log_details);
    $log_stmt->execute();

    $_SESSION['success'] = "Book returned successfully!";
    header("Location: ../my_issues.php");
    exit;
}
