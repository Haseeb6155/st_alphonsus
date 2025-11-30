<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) { header("Location: library.php"); exit; }
$id = $_GET['id'];

// Fetch Book
$stmt = $pdo->prepare("SELECT * FROM Library_Books WHERE book_id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year_published']);
    $available = $_POST['available'];

    if (empty($title) || empty($author)) {
        $message = "<div class='status-pill status-inactive'>Title and Author required!</div>";
    } else {
        try {
            $sql = "UPDATE Library_Books SET title=:title, author=:author, year_published=:year, available=:avail WHERE book_id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':title' => $title, ':author' => $author, ':year' => $year, ':avail' => $available, ':id' => $id]);
            
            $message = "<div class='status-pill status-active'>Book Updated!</div>";
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM Library_Books WHERE book_id = :id");
            $stmt->execute([':id' => $id]);
            $book = $stmt->fetch();
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
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
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Edit Book Details</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Title</label><input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>"></div>
            <div class="form-group"><label>Author</label><input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>"></div>
            <div class="form-group"><label>Year</label><input type="text" name="year_published" value="<?= htmlspecialchars($book['year_published']) ?>"></div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="available">
                    <option value="1" <?= $book['available'] == 1 ? 'selected' : '' ?>>Available</option>
                    <option value="0" <?= $book['available'] == 0 ? 'selected' : '' ?>>Checked Out</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Book</button>
            <a href="library.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>