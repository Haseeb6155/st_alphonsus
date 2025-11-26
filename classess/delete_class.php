<?php
include '../db.php';

// 1. Check if an ID was provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 2. Prepare the DELETE statement
        $sql = "DELETE FROM classes WHERE class_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // 3. Redirect back to the list
        header("Location: classes.php");
        exit;

    } catch (PDOException $e) {
        // 4. Handle "Foreign Key" Error
        // If pupils are assigned to this class, the database won't let you delete it.
        if ($e->getCode() == 23000) {
            echo "<h2>Error!</h2>";
            echo "<p>Cannot delete this class because <b>Pupils are currently assigned to it</b>.</p>";
            echo "<p>Please re-assign those pupils to a different class first.</p>";
            echo "<a href='classes.php'>Back to Class List</a>";
        } else {
            echo "Error deleting record: " . $e->getMessage();
        }
    }
} else {
    header("Location: classes.php");
    exit;
}
?>