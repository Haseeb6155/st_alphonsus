<?php
include '../db.php';

// 1. Check if an ID was provided in the link
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. Try to delete the parent
        $sql = "DELETE FROM Parents WHERE parent_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // 3. If successful, go back to the list
        header("Location: parents.php");
        exit;

    } catch (PDOException $e) {
        // 4. Handle "Foreign Key" Error (Code 23000)
        // This happens if the parent is still linked to a pupil in the database
        if ($e->getCode() == 23000) {
            echo "<h2>Error!</h2>";
            echo "<p>Cannot delete this parent because they are <b>linked to a pupil</b>.</p>";
            echo "<a href='parents.php'>Back to Parent List</a>";
        } else {
            echo "Error deleting record: " . $e->getMessage();
        }
    }
} else {
    // If no ID, kick them back to the list
    header("Location: parents.php");
    exit;
}
?>