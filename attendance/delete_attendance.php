<?php
include '../db.php';

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- SECURITY CHECKS ---

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit(); // Stop script execution to prevent unauthorized access
}

// Check user role: Parents should not be able to delete data
if ($_SESSION['role'] == 'parent') {
    die("Access Denied: Only teachers or admins can delete records.");
}

// --- DELETE LOGIC ---

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Use a prepared statement to securely delete the record by ID
        $sql = "DELETE FROM Attendance WHERE attendance_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Redirect back to the main page after deletion
        header("Location: attendance.php?msg=deleted");
        exit;

    } catch (PDOException $e) {
        // Handle any database errors
        die("Action Failed: " . $e->getMessage());
    }
} else {
    // Redirect if no ID was provided in the URL
    header("Location: attendance.php");
    exit();
}
?>