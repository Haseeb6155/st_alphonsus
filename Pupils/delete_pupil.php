<?php
// Include the database connection so we can communicate with the server
include '../db.php';

// Check if the 'id' parameter is set in the URL.
// We strictly need an ID to know which record to remove.
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Prepare the DELETE SQL statement.
        // Using a prepared statement with :id protects the database from SQL injection attacks.
        $sql = "DELETE FROM Pupils WHERE pupil_id = :id";
        $stmt = $pdo->prepare($sql);
        
        // Execute the statement passing the actual ID value
        $stmt->execute([':id' => $id]);
        
        // Once deleted, immediately redirect the user back to the main list.
        // This provides a smoother user experience than showing a blank 'success' page.
        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        // If the database throws an error (e.g., trying to delete a pupil who is linked to other records),
        // display the error message for debugging.
        echo "Error deleting record: " . $e->getMessage();
    }

} else {
    // If a user tries to access this page directly without an ID (e.g., typing the URL manually),
    // redirect them safely back to the homepage.
    header("Location: index.php");
    exit;
}
?>