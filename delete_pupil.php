<?php
include 'db.php';

// 1. Check if an ID was sent
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 2. The Delete Query
    try {
        $sql = "DELETE FROM Pupils WHERE pupil_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // 3. Go back to the main list automatically
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error deleting record: " . $e->getMessage();
    }
} else {
    // If someone tries to visit this page without clicking a button
    header("Location: index.php");
}
?>