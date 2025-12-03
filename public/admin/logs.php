<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$logs = $conn->query("SELECT l.*, u.name FROM logs l LEFT JOIN users u ON l.user_id = u.id WHERE l.action IN ('Book Issued', 'Book Returned') ORDER BY l.id DESC LIMIT 100");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-logs.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div><span><?php echo htmlspecialchars($_SESSION['name']); ?></span><a href="../logout.php">Logout</a></div>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h2 style="margin-bottom: 20px;">Activity Logs</h2>
        <table>
            <thead><tr><th>User</th><th>Action</th><th>Details</th><th>Timestamp</th></tr></thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                <tr><td><?php echo htmlspecialchars($log['name'] ?? 'System'); ?></td><td>
                    <?php echo htmlspecialchars($log['action']); ?></td><td>
                    <?php echo htmlspecialchars($log['details'] ?? '-'); ?></td><td>
                    <?php echo $log['created_at']; ?></td></tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
