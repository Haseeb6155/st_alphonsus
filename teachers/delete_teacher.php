<?php
session_start();
include '../db.php';

// Verify authentication: Only administrators are authorized to delete teacher accounts
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit(); 
}

if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];

    try {
        // Retrieve associated User ID to ensure complete account removal
        $stmt = $pdo->prepare("SELECT user_id FROM teachers WHERE teacher_id = :id");
        $stmt->execute([':id' => $teacher_id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teacher && $teacher['user_id']) {
            // Delete user account; database cascade will automatically remove the teacher profile
            $del_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
            $del_stmt->execute([':uid' => $teacher['user_id']]);
        } else {
            // Fallback: Delete teacher record directly if no user login is linked
            $del_stmt = $pdo->prepare("DELETE FROM teachers WHERE teacher_id = :id");
            $del_stmt->execute([':id' => $teacher_id]);
        }
        
        header("Location: teachers.php");
        exit;

    } catch (PDOException $e) {
        // Handle foreign key violations (e.g., Teacher is currently assigned to a Class)
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"><title>Action Failed</title>
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
    // Redirect if accessed without a valid ID
    header("Location: teachers.php");
    exit();
}
?>