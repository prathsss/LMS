<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../index.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$books = $conn->query("SELECT * FROM books WHERE available_quantity > 0 ORDER BY title ASC");

// Get books already issued by this member
$issued_books = [];
$issued_result = $conn->query("SELECT book_id FROM book_issues WHERE member_id = $member_id AND status = 'issued'");
while ($row = $issued_result->fetch_assoc()) {
    $issued_books[] = $row['book_id'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/member-books.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div><span><?php echo htmlspecialchars($_SESSION['name']); ?></span><a href="../logout.php">Logout</a></div>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message" style="background: rgba(40, 200, 40, 0.9); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <span>✓</span>
                <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" style="background: rgba(200, 40, 40, 0.9); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <span>⚠️</span>
                <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="header">
            <h2>Available Books</h2>
        </div>

        <div class="books-grid">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="book-card">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_id'] ?? 'N/A'); ?></p>
                    <p><strong>Available:</strong> <?php echo $book['available_quantity']; ?> copies</p>
                    <?php if (!empty($book['isbn'])): ?>
                        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                    <?php endif; ?>

                    <?php if (in_array($book['id'], $issued_books)): ?>
                        <button class="btn" disabled>Already Issued</button>
                    <?php else: ?>
                        <a href="controllers/MemberIssueController.php?issue=<?php echo $book['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to issue this book?')">Issue Book</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($books->num_rows === 0): ?>
            <div style="text-align: center; padding: 40px; color: #cccccc;">
                <p>No books are currently available for issue.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

