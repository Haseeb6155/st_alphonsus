<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM Pupils WHERE pupil_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: index.php");
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
                <h2 style="color: var(--danger);">Delete Failed</h2>
                <p style="color: var(--text-muted); margin: 20px 0;">
                    Cannot delete this pupil because they have <b>attendance records</b> or are linked to a <b>parent</b>.<br>
                    You must remove those records first.
                </p>
                <a href="index.php" class="btn btn-primary">Back to List</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    header("Location: index.php");
}
?>