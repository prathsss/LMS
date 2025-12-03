<?php
require_once "config/db.php";
require_once "config/config.php";

echo "<h2>Database Check</h2>";

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✓ Database connection successful<br>";

// List all tables
$result = $conn->query("SHOW TABLES");
if ($result) {
    echo "<h3>Tables in library_mgmt:</h3>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
} else {
    echo "✗ Error listing tables<br>";
}

// Check record counts
$tables = ['users', 'categories', 'books', 'book_issues', 'logs'];
echo "<h3>Record Counts:</h3>";
foreach ($tables as $table) {
    $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($count_result) {
        $count = $count_result->fetch_assoc()['count'];
        echo "$table: $count records<br>";
    } else {
        echo "$table: Error getting count<br>";
    }
}

// Check admin users
echo "<h3>Admin Users:</h3>";
$admin_result = $conn->query("SELECT name, email FROM users WHERE role = 'admin'");
if ($admin_result && $admin_result->num_rows > 0) {
    while ($admin = $admin_result->fetch_assoc()) {
        echo "- " . $admin['name'] . " (" . $admin['email'] . ")<br>";
    }
} else {
    echo "No admin users found<br>";
}

// Check sample books
echo "<h3>Sample Books (first 5):</h3>";
$books_result = $conn->query("SELECT title, author, quantity, available_quantity FROM books LIMIT 5");
if ($books_result && $books_result->num_rows > 0) {
    while ($book = $books_result->fetch_assoc()) {
        echo "- " . $book['title'] . " by " . $book['author'] . " (Qty: " . $book['quantity'] . ", Available: " . $book['available_quantity'] . ")<br>";
    }
} else {
    echo "No books found<br>";
}

$conn->close();
?>
