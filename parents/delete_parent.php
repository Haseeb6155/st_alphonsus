<?php
session_start();

// Check if the user is logged in AND if they are an admin
// (You can adjust 'admin' to 'teacher' if teachers should also have access)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not authorized, kick them back to login
    header("Location: ../login.php"); 
    exit;
}
// ... existing code follows ...
include '../db.php';

if (isset($_GET['id'])) {
    $parent_id = $_GET['id'];

    try {
        // 1. Find the linked User ID
        $stmt = $pdo->prepare("SELECT user_id FROM Parents WHERE parent_id = :id");
        $stmt->execute([':id' => $parent_id]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parent && $parent['user_id']) {
            // 2. Delete the USER. 
            // The DB "ON DELETE CASCADE" rule will automatically delete the Parent Profile too!
            $del_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
            $del_stmt->execute([':uid' => $parent['user_id']]);
        } else {
            // Fallback for old records
            $del_stmt = $pdo->prepare("DELETE FROM Parents WHERE parent_id = :id");
            $del_stmt->execute([':id' => $parent_id]);
        }
        
        header("Location: parents.php");
        exit;

    } catch (PDOException $e) {
        // Error handling
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: parents.php");
}
?>