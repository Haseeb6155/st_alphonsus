<?php
// Include the database connection file to establish a link to the DB
include '../db.php';

// Prepare the SQL query to fetch pupil details.
// We perform an INNER JOIN with the 'Classes' table to retrieve the actual
// 'class_name' (e.g., "Year One") instead of just the numeric 'class_id'.
$sql = "SELECT Pupils.pupil_id, Pupils.full_name, Classes.class_name, Pupils.medical_info, Pupils.address
        FROM Pupils
        JOIN Classes ON Pupils.class_id = Classes.class_id";

// Execute the query and fetch all results into an associative array
$stmt = $pdo->query($sql);
$pupils = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Pupil Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>St Alphonsus Primary School - Pupil List</h1>

    <a href="add_pupil.php" class="add-btn">+ Add New Pupil</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Class</th>
                <th>Medical Info</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pupils as $pupil): ?>
                <tr>
                    <td><?= htmlspecialchars($pupil['pupil_id']) ?></td>
                    <td><?= htmlspecialchars($pupil['full_name']) ?></td>
                    <td><?= htmlspecialchars($pupil['class_name']) ?></td>
                    <td><?= htmlspecialchars($pupil['address']) ?></td>
                    <td><?= htmlspecialchars($pupil['medical_info']) ?></td>
                    <td>
                        <a href="edit_pupil.php?id=<?= $pupil['pupil_id'] ?>" class="edit-link">Edit</a>
                        |
                        <a href="delete_pupil.php?id=<?= $pupil['pupil_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>