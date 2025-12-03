<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['get'])) {
    // Fetch book data for editing
    $id = intval($_GET['get']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        header('Content-Type: application/json');
        echo json_encode($book);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
    }
    exit;
}

if (isset($_GET['delete'])) {
    // Delete book
    $id = intval($_GET['delete']);

    // Check if book is currently issued
    $stmt = $conn->prepare("SELECT COUNT(*) as issued_count FROM issues WHERE book_id = ? AND status = 'issued'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $issued = $result->fetch_assoc();

    if ($issued['issued_count'] > 0) {
        $_SESSION['error'] = "Cannot delete book. It is currently issued to {$issued['issued_count']} member(s).";
        header("Location: ../admin/books.php");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Book deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting book.";
    }

    header("Location: ../admin/books.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        // Add new book
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category_id = intval($_POST['category_id']);
        $quantity = intval($_POST['quantity']);
        $isbn = trim($_POST['isbn']) ?: null;

        if (empty($title) || empty($author) || $category_id <= 0 || $quantity <= 0) {
            $_SESSION['error'] = "All required fields must be filled correctly.";
            header("Location: ../admin/books.php");
            exit;
        }

        // Insert book
        $stmt = $conn->prepare("INSERT INTO books (title, author, category_id, quantity, available_quantity, isbn) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $title, $author, $category_id, $quantity, $quantity, $isbn);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Book added successfully!";
        } else {
            $_SESSION['error'] = "Error adding book.";
        }

        header("Location: ../admin/books.php");
        exit;
    }

    if ($_POST['action'] === 'update') {
        // Update book
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category_id = intval($_POST['category_id']);
        $quantity = intval($_POST['quantity']);
        $isbn = trim($_POST['isbn']) ?: null;

        if (empty($title) || empty($author) || $category_id <= 0 || $quantity <= 0) {
            $_SESSION['error'] = "All required fields must be filled correctly.";
            header("Location: ../admin/books.php");
            exit;
        }

        // Get current quantity to adjust available quantity
        $stmt = $conn->prepare("SELECT quantity, available_quantity FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();

        $quantity_diff = $quantity - $current['quantity'];
        $new_available = $current['available_quantity'] + $quantity_diff;

        // Ensure available quantity doesn't go negative
        if ($new_available < 0) {
            $_SESSION['error'] = "Cannot reduce quantity below currently issued books.";
            header("Location: ../admin/books.php");
            exit;
        }

        // Update book
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category_id = ?, quantity = ?, available_quantity = ?, isbn = ? WHERE id = ?");
        $stmt->bind_param("ssiissi", $title, $author, $category_id, $quantity, $new_available, $isbn, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Book updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating book.";
        }

        header("Location: ../admin/books.php");
        exit;
    }
}

// If no valid action, redirect back
header("Location: ../admin/books.php");
exit;
?>
