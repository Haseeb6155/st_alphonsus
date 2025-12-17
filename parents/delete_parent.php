<?php
session_start();
include '../db.php';

// --- SECURITY CHECK ---
// Verify authentication and authorization: Only administrators are permitted to delete parent accounts.
// Redirect unauthorized users to the homepage.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

if (isset($_GET['id'])) {
    $parent_id = $_GET['id'];

    try {
        // Retrieve the associated User ID to ensure complete account removal
        $stmt = $pdo->prepare("SELECT user_id FROM Parents WHERE parent_id = :id");
        $stmt->execute([':id' => $parent_id]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parent && $parent['user_id']) {
            // Delete the login credential from the 'users' table.
            // This relies on the database's ON DELETE CASCADE constraint to automatically remove the linked Parent profile.
            $del_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
            $del_stmt->execute([':uid' => $parent['user_id']]);
        } else {
            // Fallback: Directly delete the parent record if no associated user account exists or was found
            $del_stmt = $pdo->prepare("DELETE FROM Parents WHERE parent_id = :id");
            $del_stmt->execute([':id' => $parent_id]);
        }
        
        // Redirect to the parent list upon successful deletion
        header("Location: parents.php");
        exit;

    } catch (PDOException $e) {
        // --- ERROR HANDLING ---
        // Catch Foreign Key constraint violations.
        // This typically occurs if the parent is still linked to a Pupil via the link table.
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
                    Cannot delete this parent.<br>
                    They might still be linked to a <b>Student</b>. Please unlink them first.
                </p>
                <a href="parents.php" class="btn btn-primary">Back to List</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    // Redirect if accessed without a valid ID parameter
    header("Location: parents.php");
}
?>