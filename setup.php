<?php
require_once "config/db.php";
require_once "config/config.php";

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop existing tables
$tables = ['book_issues', 'logs', 'books', 'categories', 'users'];
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table");
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<h2>Creating Database Tables...</h2>";

// 1. Users Table
$sql_users = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users)) {
    echo "✓ Users table created<br>";
} else {
    echo "✗ Error: " . $conn->error . "<br>";
}

// 2. Categories Table
$sql_categories = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_categories)) {
    echo "✓ Categories table created<br>";
} else {
    echo "✗ Error: " . $conn->error . "<br>";
}

// 3. Books Table
$sql_books = "CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category_id INT,
    quantity INT DEFAULT 0,
    available_quantity INT DEFAULT 0,
    isbn VARCHAR(20),
    publication_year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)";

if ($conn->query($sql_books)) {
    echo "✓ Books table created<br>";
} else {
    echo "✗ Error: " . $conn->error . "<br>";
}

// 4. Book Issues Table
$sql_issues = "CREATE TABLE book_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    issue_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('issued', 'returned', 'overdue') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql_issues)) {
    echo "✓ Book Issues table created<br>";
} else {
    echo "✗ Error: " . $conn->error . "<br>";
}

// 5. Logs Table
$sql_logs = "CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql_logs)) {
    echo "✓ Logs table created<br>";
} else {
    echo "✗ Error: " . $conn->error . "<br>";
}

echo "<h2>Inserting Sample Data...</h2>";

// Insert Admin User
$admin_email = "admin@library.com";
$admin_password = password_hash("admin123", PASSWORD_BCRYPT);
$admin_name = "Administrator";
$admin_role = "admin";

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $admin_name, $admin_email, $admin_password, $admin_role);

if ($stmt->execute()) {
    echo "✓ Admin user created (Email: admin@library.com, Password: admin123)<br>";
} else {
    echo "✗ Error: " . $stmt->error . "<br>";
}

// Insert Sample Categories
$categories = [
    ['Fiction', 'Fiction books and novels'],
    ['Non-Fiction', 'Non-fiction and educational books'],
    ['Science', 'Science and technology books'],
    ['History', 'History and biography books'],
    ['Education', 'Educational textbooks']
];

foreach ($categories as $cat) {
    $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $cat[0], $cat[1]);
    $stmt->execute();
}
echo "✓ Sample categories inserted<br>";

// Insert Sample Books
$sample_books = [
    ['The Great Gatsby', 'F. Scott Fitzgerald', 1, 5, 5, '978-0-7432-7356-5', 1925],
    ['To Kill a Mockingbird', 'Harper Lee', 1, 3, 3, '978-0-06-112008-4', 1960],
    ['1984', 'George Orwell', 1, 4, 4, '978-0-451-52494-2', 1949],
    ['A Brief History of Time', 'Stephen Hawking', 3, 2, 2, '978-0-553-38016-3', 1988],
    ['The Selfish Gene', 'Richard Dawkins', 3, 3, 3, '978-0-19-286092-0', 1976],
    ['Sapiens', 'Yuval Noah Harari', 2, 4, 4, '978-0-06-231609-7', 2014],
    ['The Diary of Anne Frank', 'Anne Frank', 4, 6, 6, '978-0-553-29438-2', 1947]
];

foreach ($sample_books as $book) {
    $stmt = $conn->prepare("INSERT INTO books (title, author, category_id, quantity, available_quantity, isbn, publication_year) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiis", $book[0], $book[1], $book[2], $book[3], $book[4], $book[5], $book[6]);
    $stmt->execute();
}
echo "✓ Sample books inserted<br>";

echo "<h2 style='color: green;'>Database Setup Complete!</h2>";
echo "<p><strong>Login Credentials:</strong><br>";
echo "Email: admin@library.com<br>";
echo "Password: admin123</p>";
echo "<p><a href='index.php'>Go to Login Page</a></p>";

$conn->close();
?>
