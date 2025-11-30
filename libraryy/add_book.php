<?php
include '../db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year_published']);

    if (empty($title) || empty($author)) {
        $message = "<div class='status-pill status-inactive'>Title and Author are required!</div>";
    } elseif (!empty($year) && !is_numeric($year)) {
        $message = "<div class='status-pill status-inactive'>Year must be a number!</div>";
    } else {
        try {
            // Default 'available' to 1 (True)
            $sql = "INSERT INTO Library_Books (title, author, year_published, available) VALUES (:title, :author, :year, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':title' => $title, ':author' => $author, ':year' => $year]);

            $message = "<div class='status-pill status-active'>Book Added Successfully!</div>";
            header("refresh:1;url=library.php");
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Database Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Add New Book</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Book Title</label><input type="text" name="title" placeholder="e.g. Harry Potter"></div>
            <div class="form-group"><label>Author</label><input type="text" name="author" placeholder="e.g. J.K. Rowling"></div>
            <div class="form-group"><label>Year Published</label><input type="text" name="year_published" placeholder="e.g. 1997"></div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add to Library</button>
            <a href="library.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>