<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$members = $conn->query("SELECT id, name, email, created_at FROM users WHERE role = 'member' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-members.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div><span><?php echo htmlspecialchars($_SESSION['name']); ?></span><a href="../logout.php">Logout</a></div>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h2 style="margin-bottom: 20px;">Manage Members</h2>
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($member = $members->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                    <td><?php echo $member['created_at']; ?></td>
                    <td>
                        <a href="#" class="action-btn edit-btn" onclick="editMember(<?php echo $member['id']; ?>)">Edit</a>
                        <a href="controllers/MemberController.php?delete=<?php echo $member['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this member?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Edit Member Modal -->
        <div id="editModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Edit Member</h2>
                <form id="editForm" method="POST" action="../controllers/MemberController.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editId">

                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" id="editName" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>

                    <button type="submit" class="btn">Update Member</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editMember(id) {
            // Fetch member data via AJAX or redirect to edit page
            // For simplicity, we'll use a simple approach
            fetch(`../controllers/MemberController.php?get=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Error fetching member data');
                });
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
