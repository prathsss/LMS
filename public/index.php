<?php
require_once "../config/config.php";
require_once "../config/db.php";

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: member/dashboard.php");
    }
    exit;
}

$books = $conn->query("SELECT books.*, categories.category_name FROM books LEFT JOIN categories ON books.category_id = categories.id WHERE books.available_quantity > 0 ORDER BY books.title ASC LIMIT 12");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/member-books.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Welcome to Our Library</h2>
            <p>Explore our collection of books. Login to issue books and manage your borrowings.</p>
        </div>

        <div class="header">
            <h2>Available Books</h2>
        </div>

        <div class="books-grid">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="book-card">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></p>
                    <p><strong>Available:</strong> <?php echo $book['available_quantity']; ?> copies</p>
                    <?php if (!empty($book['isbn'])): ?>
                        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                    <?php endif; ?>

                    <a href="login.php" class="btn">Login to Issue</a>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($books->num_rows === 0): ?>
            <div style="text-align: center; padding: 40px; color: #cccccc;">
                <p>No books are currently available.</p>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px; padding: 20px; background: rgba(40, 40, 40, 0.9); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="color: #ffffff; margin-bottom: 15px;">Join Our Library Today!</h3>
            <p style="color: #cccccc; margin-bottom: 20px;">Create an account to start borrowing books and managing your reading journey.</p>
            <div>
                <a href="register.php" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, rgba(140, 140, 140, 1) 0%, rgba(100, 100, 100, 1) 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 0 10px; transition: all 0.3s ease;">Register Now</a>
                <a href="login.php" style="display: inline-block; padding: 12px 24px; background: transparent; color: #cccccc; text-decoration: none; border: 2px solid #cccccc; border-radius: 8px; font-weight: 600; margin: 0 10px; transition: all 0.3s ease;">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
