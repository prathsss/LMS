<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}


$total_books = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$total_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'member'")->fetch_assoc()['count'];
$issued_books = $conn->query("SELECT COUNT(*) as count FROM book_issues WHERE status = 'issued'")->fetch_assoc()['count'];
$categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-dashboard.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (Admin)</span>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Dashboard Overview</h2>
            <p>Manage your library system from here</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Books</h3>
                <div class="number"><?php echo $total_books; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Members</h3>
                <div class="number"><?php echo $total_members; ?></div>
            </div>
            <div class="stat-card">
                <h3>Issued Books</h3>
                <div class="number"><?php echo $issued_books; ?></div>
            </div>
            <div class="stat-card">
                <h3>Categories</h3>
                <div class="number"><?php echo $categories; ?></div>
            </div>
        </div>

        <h2 style="margin-bottom: 20px;">Management Options</h2>
        <div class="menu-grid">
            <div class="menu-card">
                <a href="books.php">
                    <div class="icon">📚</div>
                    <h3>Manage Books</h3>
                </a>
            </div>

            <div class="menu-card">
                <a href="members.php">
                    <div class="icon">👥</div>
                    <h3>Manage Members</h3>
                </a>
            </div>
            <div class="menu-card">
                <a href="issues.php">
                    <div class="icon">🔄</div>
                    <h3>Book Issues</h3>
                </a>
            </div>
            <div class="menu-card">
                <a href="logs.php">
                    <div class="icon">📋</div>
                    <h3>View Logs</h3>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

