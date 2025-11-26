<?php
include '../db.php';
$message = "";

// 1. Check for ID
if (!isset($_GET['id'])) {
    header("Location: library.php");
    exit;
}

$id = $_GET['id'];

// 2. Fetch Current Book Data
$sql = "SELECT * FROM Library_Books WHERE book_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) { die("Book not found!"); }

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year_published']);
    $available = $_POST['available']; // This will be 1 or 0

    if (empty($title) || empty($author)) {
        $message = "<p style='color: red;'>Title and Author are required!</p>";
    } else {
        try {
            $sql = "UPDATE Library_Books 
                    SET title = :title, 
                        author = :author, 
                        year_published = :year, 
                        available = :available 
                    WHERE book_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':year' => $year,
                ':available' => $available,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Book details updated.</p>";
            
            // Refresh Data
            $stmt = $pdo->prepare("SELECT * FROM Library_Books WHERE book_id = :id");
            $stmt->execute([':id' => $id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Edit Book: <?= htmlspecialchars($book['title']) ?></h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Book Title: *</label>
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>">

        <label>Author: *</label>
        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>">

        <label>Year Published:</label>
        <input type="text" name="year_published" value="<?= htmlspecialchars($book['year_published']) ?>">

        <label>Available?</label>
        <select name="available">
            <option value="1" <?= $book['available'] == 1 ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= $book['available'] == 0 ? 'selected' : '' ?>>No</option>
        </select>

        <button type="submit">Update Book</button>
    </form>

    <a href="library.php" class="back-link">← Back to Library</a>

</body>
</html>