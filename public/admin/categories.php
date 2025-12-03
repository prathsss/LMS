<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-categories.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div><span><?php echo htmlspecialchars($_SESSION['name']); ?></span><a href="../logout.php">Logout</a></div>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <div class="header"><h2>Manage Categories</h2></div>
        <table>
            <thead><tr><th>Category</th><th>Description</th><th>Created</th></tr></thead>
            <tbody>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                <tr><td><?php echo htmlspecialchars($cat['category_name']); ?></td><td><?php echo htmlspecialchars($cat['description'] ?? ''); ?></td><td><?php echo $cat['created_at']; ?></td></tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

