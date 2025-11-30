<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM teachers WHERE teacher_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: teachers.php");
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
                    This teacher cannot be deleted because they are currently assigned to a <b>Class</b>.
                </p>
                <a href="teachers.php" class="btn btn-primary">Back to Teachers</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    header("Location: teachers.php");
}
?>