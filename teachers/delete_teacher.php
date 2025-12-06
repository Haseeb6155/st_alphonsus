<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}
include '../db.php';

if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];

    try {
        // 1. Find the linked User ID first
        $stmt = $pdo->prepare("SELECT user_id FROM teachers WHERE teacher_id = :id");
        $stmt->execute([':id' => $teacher_id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teacher && $teacher['user_id']) {
            // 2. Delete the USER. 
            // The DB "ON DELETE CASCADE" rule will automatically delete the Teacher Profile too!
            $del_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
            $del_stmt->execute([':uid' => $teacher['user_id']]);
        } else {
            // Fallback: If no user linked, just delete the teacher record
            $del_stmt = $pdo->prepare("DELETE FROM teachers WHERE teacher_id = :id");
            $del_stmt->execute([':id' => $teacher_id]);
        }
        
        header("Location: teachers.php");
        exit;

    } catch (PDOException $e) {
        // Handle "Foreign Key" Error (e.g. Teacher is assigned to a class)
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
                    Cannot delete this teacher because they are currently assigned to a <b>Class</b>.
                    <br>Please reassign the class to another teacher first.
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