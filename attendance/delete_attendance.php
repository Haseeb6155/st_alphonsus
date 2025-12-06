<?php
include '../db.php';

// Check role security
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'parent') {
    die("Access Denied");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM Attendance WHERE attendance_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: attendance.php");
        exit;
    } catch (PDOException $e) {
        die("Error deleting record: " . $e->getMessage());
    }
} else {
    header("Location: attendance.php");
}
?>