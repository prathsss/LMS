<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$issues = $conn->query("SELECT bi.*, b.title, u.name FROM book_issues bi LEFT JOIN books b ON bi.book_id = b.id LEFT JOIN users u ON bi.member_id = u.id ORDER BY bi.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Issues - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-issues.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div><span><?php echo htmlspecialchars($_SESSION['name']); ?></span><a href="../logout.php">Logout</a></div>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h2 style="margin-bottom: 20px;">Book Issues</h2>
        <table>
            <thead><tr><th>Book</th><th>Member</th><th>Issue Date</th><th>Due Date</th><th>Status</th></tr></thead>
            <tbody>
                <?php while ($issue = $issues->fetch_assoc()): ?>
                <tr><td><?php echo htmlspecialchars($issue['title'] ?? 'N/A'); ?></td><td><?php echo htmlspecialchars($issue['name'] ?? 'N/A'); ?></td><td><?php echo $issue['issue_date']; ?></td><td><?php echo $issue['due_date']; ?></td><td><?php echo $issue['status']; ?></td></tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
