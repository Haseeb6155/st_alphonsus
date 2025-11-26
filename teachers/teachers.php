<?php
include '../db.php';

// Select the specific columns we want to show for teachers
$sql = "SELECT teacher_id, full_name, phone, address FROM teachers";

$stmt = $pdo->query($sql);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Teacher Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>St Alphonsus - Teacher List</h1>

    <a href="add_teacher.php" class="add-btn">+ Add New Teacher</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?= htmlspecialchars($teacher['teacher_id']) ?></td>
                    <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                    <td><?= htmlspecialchars($teacher['phone']) ?></td>
                    <td><?= htmlspecialchars($teacher['address']) ?></td>
                    <td>
                        <a href="edit_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="edit-link">Edit</a>
    |
                        <a href="delete_teacher.php?id=<?= $teacher['teacher_id'] ?>" class="delete-link" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>