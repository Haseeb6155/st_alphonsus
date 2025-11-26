<?php
include '../db.php';

// 1. Check if an ID was provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. Prepare the DELETE statement
        // We use the teacher_id to ensure we only remove the specific record clicked.
        $sql = "DELETE FROM teachers WHERE teacher_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // 3. Redirect back to the list
        header("Location: teachers.php");
        exit;

    } catch (PDOException $e) {
        // If the teacher is linked to a class, the database might stop us (Foreign Key check)
        echo "Error deleting record (They might still be assigned to a class): " . $e->getMessage();
    }
} else {
    header("Location: teachers.php");
    exit;
}
?>