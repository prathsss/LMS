<?php
require_once "../../config/config.php";
require_once "../../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$books = $conn->query("SELECT books.*, categories.category_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY books.id DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-books.css">
</head>
<body>
    <div class="navbar">
        <h1>Library Management System</h1>
        <div>
            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <span>✓</span>
                <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <span>⚠️</span>
                <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="header">
            <h2>Manage Books</h2>
            <a href="#" class="btn">+ Add New Book</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>ISBN</th>
                    <th>Action</th>
                </tr>
             </thead>
            <tbody>
                <?php while ($book = $books->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo $book['quantity']; ?></td>
                    <td><?php echo $book['available_quantity']; ?></td>
                    <td><?php echo htmlspecialchars($book['isbn'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="#" class="action-btn edit-btn" onclick="editBook(<?php echo $book['id']; ?>)">Edit</a>
                        <a href="controllers/BookController.php?delete=<?php echo $book['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Edit Book Form Container -->
        <div class="add-book-container" id="addBookContainer" style="display: none;">
            <div class="add-book-header">
                <h2 id="formTitle">📚 Add New Book</h2>
                <p id="formSubtitle">Enter the book details below</p>
            </div>

            <form method="POST" action="../controllers/BookController.php" class="add-book-form" id="bookForm">
                <input type="hidden" name="action" value="add" id="formAction">
                <input type="hidden" name="id" id="bookId">

                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" id="title" name="title" required placeholder="Enter book title">
                    <span class="input-focus"></span>
                </div>

                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" required placeholder="Enter author name">
                    <span class="input-focus"></span>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php
                        $categories->data_seek(0); // Reset pointer
                        while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <span class="input-focus"></span>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" required placeholder="Enter quantity" min="1">
                    <span class="input-focus"></span>
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN (Optional)</label>
                    <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN">
                    <span class="input-focus"></span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="add-book-btn" id="submitBtn">Add Book</button>
                    <button type="button" class="cancel-btn" onclick="closeBookForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle add book form
        document.querySelector('.btn').addEventListener('click', function(e) {
            e.preventDefault();
            resetBookForm();
            document.getElementById('addBookContainer').style.display = 'block';
        });

        function editBook(id) {
            fetch(`../controllers/BookController.php?get=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('formTitle').textContent = '📚 Edit Book';
                    document.getElementById('formSubtitle').textContent = 'Update the book details below';
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('bookId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('author').value = data.author;
                    document.getElementById('category_id').value = data.category_id;
                    document.getElementById('quantity').value = data.quantity;
                    document.getElementById('isbn').value = data.isbn || '';
                    document.getElementById('submitBtn').textContent = 'Update Book';
                    document.getElementById('addBookContainer').style.display = 'block';
                })
                .catch(error => {
                    alert('Error fetching book data');
                });
        }

        function closeBookForm() {
            document.getElementById('addBookContainer').style.display = 'none';
            resetBookForm();
        }

        function resetBookForm() {
            document.getElementById('formTitle').textContent = '📚 Add New Book';
            document.getElementById('formSubtitle').textContent = 'Enter the book details below';
            document.getElementById('formAction').value = 'add';
            document.getElementById('bookId').value = '';
            document.getElementById('bookForm').reset();
            document.getElementById('submitBtn').textContent = 'Add Book';
        }
    </script>
</body>
</html>
