<?php
session_start();

// Check if the user is logged in AND if they are an admin
// (You can adjust 'admin' to 'teacher' if teachers should also have access)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not authorized, kick them back to login
    header("Location: ../login.php"); 
    exit;
}

include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM classes WHERE class_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: classes.php");
        exit;
    } catch (PDOException $e) {
        // ERROR UI
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
                    <?php 
                    if ($e->getCode() == 23000) {
                        echo "You cannot delete this class because it has <b>Pupils</b> assigned to it.<br>Please move the pupils to another class first.";
                    } else {
                        echo $e->getMessage();
                    }
                    ?>
                </p>
                <a href="classes.php" class="btn btn-primary">Back to Classes</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    header("Location: classes.php");
}
?>