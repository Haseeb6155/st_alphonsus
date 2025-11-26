<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM Library_Books WHERE book_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: library.php");
        exit;

    } catch (PDOException $e) {
        // Handle constraint errors (e.g. if a book is currently loaned out)
        echo "Error deleting book: " . $e->getMessage();
    }
} else {
    header("Location: library.php");
    exit;
}
?>