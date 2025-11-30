<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM Library_Books WHERE book_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: library.php");
        exit;
    } catch (PDOException $e) {
        // Show error nicely
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head><meta charset="UTF-8"><title>Error</title><link rel="stylesheet" href="../style.css"></head>
        <body class="centered-layout">
            <div class="form-card" style="text-align: center;">
                <h2 style="color: var(--danger);">Error</h2>
                <p><?= $e->getMessage() ?></p>
                <a href="library.php" class="btn btn-primary">Back</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    header("Location: library.php");
}
?>