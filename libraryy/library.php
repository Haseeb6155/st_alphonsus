<?php
/*
    LIBRARY DASHBOARD
    -----------------
    Displays the book catalog with availability status.
*/
include '../db.php';

// Check role
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['role'] ?? 'guest';

// Fetch Books
$sql = "SELECT book_id, title, author, year_published, available FROM Library_Books";
$stmt = $pdo->query($sql);
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Library</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>School Library</h1>
            <?php if ($role != 'parent'): ?>
                <a href="add_book.php" class="btn btn-primary">+ Add New Book</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th>Status</th>
                        <?php if ($role != 'parent'): ?>
                            <th style="text-align: right;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td style="color: var(--text-muted);">
                                #<?= htmlspecialchars($book['book_id']) ?>
                            </td>
                            <td style="font-weight: 500; color: var(--text-main);">
                                <?= htmlspecialchars($book['title']) ?>
                            </td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['year_published']) ?></td>
                            
                            <!-- Status Pill Logic -->
                            <td>
                                <?php if ($book['available']): ?>
                                    <span class="status-pill status-active">Available</span>
                                <?php else: ?>
                                    <span class="status-pill status-inactive">Checked Out</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if ($role != 'parent'): ?>
                                <td style="text-align: right;">
                                    <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="btn btn-sm" style="background: #374151;">Edit</a>
                                    <a href="delete_book.php?id=<?= $book['book_id'] ?>" 
                                       class="btn btn-sm" 
                                       style="background: rgba(239,68,68,0.2); color: #f87171;" 
                                       onclick="return confirm('Delete this book?');">Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
                                <?php include '../footer.php'; ?>
</body>
</html>