<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM Parents WHERE parent_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: parents.php");
        exit;
    } catch (PDOException $e) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"><title>Error</title>
            <link rel="stylesheet" href="../style.css">
        </head>
        <body class="centered-layout">
            <div class="form-card" style="text-align: center;">
                <h2 style="color: var(--danger);">Action Failed</h2>
                <p style="color: var(--text-muted); margin: 20px 0;">
                    Cannot delete this parent because they are <b>linked to a pupil</b>.<br>
                    Please remove the link before deleting the parent record.
                </p>
                <a href="parents.php" class="btn btn-primary">Back to List</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    header("Location: parents.php");
}
?>