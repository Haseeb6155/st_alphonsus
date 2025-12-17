<?php
session_start();
include '../db.php';

// Restrict access to staff and administrators only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'parent') {
    header("Location: ../index.php"); 
    exit;
}

// Ensure a valid pupil ID is provided via URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Execute deletion using prepared statement
        $sql = "DELETE FROM Pupils WHERE pupil_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Redirect to main list on success
        header("Location: index.php");
        exit;
        
    } catch (PDOException $e) {
        // Handle constraint violations (e.g., student has linked attendance/parents)
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Error</title>
            <link rel="stylesheet" href="../style.css">
        </head>
        <body class="centered-layout">
            <div class="form-card" style="text-align: center;">
                <h2 style="color: var(--danger);">Delete Failed</h2>
                
                <p style="color: var(--text-muted); margin: 20px 0;">
                    Cannot delete this pupil because they have <b>attendance records</b> or are linked to a <b>parent</b>.<br>
                    You need to remove those records first before deleting the student.
                </p>
                
                <a href="index.php" class="btn btn-primary">Back to List</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    // Redirect if accessed without an ID
    header("Location: index.php");
}
?>