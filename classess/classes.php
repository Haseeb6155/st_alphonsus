<?php
include '../db.php';

// SQL: Select all class info AND the teacher's full name
// We join the two tables so we can match the ID to the Name
$sql = "SELECT Classes.*, Teachers.full_name 
        FROM Classes 
        JOIN Teachers ON Classes.teacher_id = Teachers.teacher_id";

$stmt = $pdo->query($sql);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Classes</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>St Alphonsus - Class List</h1>
    <a href="add_class.php" class="add-btn">+ Add New Class</a>

    <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Capacity</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['class_name']) ?></td>
                    <td><?= htmlspecialchars($class['capacity']) ?></td>
                    <td><?= htmlspecialchars($class['full_name']) ?></td>
                    <td>
                        <a href="edit_class.php?id=<?= $class['class_id'] ?>" class="edit-link">Edit</a>
                        <a href="delete_class.php?id=<?= $class['class_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>