<?php
include '../db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year_published']);

    if (empty($title) || empty($author)) {
        $message = "<p style='color: red;'>Title and Author are required!</p>";
    } else {
        try {
            // We default 'available' to 1 (True) for new books
            $sql = "INSERT INTO Library_Books (title, author, year_published, available) 
                    VALUES (:title, :author, :year, 1)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':year' => $year
            ]);

            $message = "<p style='color: green;'>Success! New book added to library.</p>";
            
        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Add New Book</h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Book Title: *</label>
        <input type="text" name="title">

        <label>Author: *</label>
        <input type="text" name="author">

        <label>Year Published:</label>
        <input type="text" name="year_published" placeholder="e.g. 1997">

        <button type="submit">Add Book</button>
    </form>

    <a href="library.php" class="back-link">← Back to Library</a>

</body>
</html>