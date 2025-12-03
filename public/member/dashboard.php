o<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../index.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$total_books_issued = $conn->query("SELECT COUNT(*) as count FROM book_issues WHERE member_id = $member_id AND status = 'issued'")->fetch_assoc()['count'];
$total_books_returned = $conn->query("SELECT COUNT(*) as count FROM book_issues WHERE member_id = $member_id AND status = 'returned'")->fetch_assoc()['count'];
$available_books = $conn->query("SELECT COUNT(*) as count FROM books WHERE available_quantity > 0")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/member-dashboard.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Member Dashboard</h2>
            <p>View and manage your book borrowings</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Books Issued</h3>
                <div class="number"><?php echo $total_books_issued; ?></div>
            </div>
            <div class="stat-card">
                <h3>Books Returned</h3>
                <div class="number"><?php echo $total_books_returned; ?></div>
            </div>
            <div class="stat-card">
                <h3>Available Books</h3>
                <div class="number"><?php echo $available_books; ?></div>
            </div>
        </div>

        <h2 style="margin-bottom: 20px;">Quick Issue Books</h2>
        <div class="books-grid">
            <?php
            $quick_books = $conn->query("SELECT * FROM books WHERE available_quantity > 0 ORDER BY title ASC LIMIT 6");
            $issued_books = [];
            $issued_result = $conn->query("SELECT book_id FROM book_issues WHERE member_id = $member_id AND status = 'issued'");
            while ($row = $issued_result->fetch_assoc()) {
                $issued_books[] = $row['book_id'];
            }

            while ($book = $quick_books->fetch_assoc()):
            ?>
                <div class="book-card">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Available:</strong> <?php echo $book['available_quantity']; ?> copies</p>

                    <?php if (in_array($book['id'], $issued_books)): ?>
                        <button class="btn" disabled>Already Issued</button>
                    <?php else: ?>
                        <a href="controllers/MemberIssueController.php?issue=<?php echo $book['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to issue this book?')">Issue Book</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($quick_books->num_rows === 0): ?>
            <div style="text-align: center; padding: 20px; color: #cccccc;">
                <p>No books available for quick issue.</p>
            </div>
        <?php endif; ?>

        <h2 style="margin-bottom: 20px; margin-top: 40px;">My Issued Books</h2>
        <div class="books-grid">
            <?php
            $issued_books_query = $conn->query("SELECT bi.id, b.title, b.author, bi.due_date FROM book_issues bi LEFT JOIN books b ON bi.book_id = b.id WHERE bi.member_id = $member_id AND bi.status = 'issued' ORDER BY bi.due_date ASC");
            while ($issued_book = $issued_books_query->fetch_assoc()):
            ?>
                <div class="book-card">
                    <h3><?php echo htmlspecialchars($issued_book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($issued_book['author']); ?></p>
                    <p><strong>Due Date:</strong> <?php echo $issued_book['due_date']; ?></p>
                    <a href="controllers/MemberIssueController.php?return=<?php echo $issued_book['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to return this book?')">Return Book</a>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($issued_books_query->num_rows === 0): ?>
            <div style="text-align: center; padding: 20px; color: #cccccc;">
                <p>No books currently issued.</p>
            </div>
        <?php endif; ?>

        <h2 style="margin-bottom: 20px; margin-top: 40px;">Options</h2>
        <div class="menu-grid">
            <div class="menu-card">
                <a href="books.php">
                    <div class="icon">📚</div>
                    <h3>Browse All Books</h3>
                </a>
            </div>
            <div class="menu-card">
                <a href="my_issues.php">
                    <div class="icon">📋</div>
                    <h3>My Issues</h3>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

