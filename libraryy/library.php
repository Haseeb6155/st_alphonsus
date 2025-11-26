<?php
include '../db.php';

// SQL: Fill in the missing columns!
// We want to list the books and check if they are available.
$sql = "SELECT book_id, title, author, year_published, available FROM Library_Books";

$stmt = $pdo->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Library</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>St Alphonsus - Library Books</h1>

    <a href="add_book.php" class="add-btn">+ Add New Book</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Published</th>
                <th>Available?</th>
                <th>Actions</th>
            </tr>
        </thead>
       <tbody>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= htmlspecialchars($book['book_id']) ?></td>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td><?= htmlspecialchars($book['year_published']) ?></td>
            <td><?= $book['available'] ? 'Yes' : 'No' ?></td>
            <td>
                <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="edit-link">Edit</a>
    |
                <a href="delete_book.php?id=<?= $book['book_id'] ?>" class="delete-link" onclick="return confirm('Delete this book?');">Delete</a>
            </td>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>