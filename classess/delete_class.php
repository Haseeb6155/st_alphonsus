<?php
session_start();
include '../db.php';

// --- SECURITY CHECK ---
// Verify authorization: Only administrators are permitted to delete class records.
// Redirect unauthorized users to the homepage.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Attempt to delete the class record using a prepared statement to prevent SQL injection
        $sql = "DELETE FROM classes WHERE class_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Redirect to the dashboard upon successful deletion
        header("Location: classes.php");
        exit;
    } catch (PDOException $e) {
        // --- ERROR HANDLING ---
        // Catch database errors, specifically Foreign Key constraint violations.
        // This occurs if the class cannot be deleted because it still contains pupils.
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
                    // Error code 23000 indicates an Integrity Constraint Violation
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
    // Redirect if accessed without a valid ID parameter
    header("Location: classes.php");
}
?>