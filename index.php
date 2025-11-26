<?php
include 'db.php'; // 1. Get the key to the pantry

// 2. Write the note for the kitchen (The SQL Query)
// We want to get the pupil's name and their class name.
// We join the tables so we don't just see "Class ID: 1", but "Reception".
$sql = "SELECT Pupils.pupil_id, Pupils.full_name, Classes.class_name, Pupils.medical_info 
        FROM Pupils
        JOIN Classes ON Pupils.class_id = Classes.class_id";

// 3. Send the note and get the ingredients
$stmt = $pdo->query($sql);
$pupils = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Pupil Records</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h1>St Alphonsus Primary School - Pupil List</h1>
    <a href="add_pupil.php" style="display:inline-block; margin-bottom:10px; padding:10px; background:green; color:white; text-decoration:none;">+ Add New Pupil</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Class</th>
                <th>Medical Info</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pupils as $pupil): ?>
                <tr>
                    <td><?= htmlspecialchars($pupil['pupil_id']) ?></td>
                    <td><?= htmlspecialchars($pupil['full_name']) ?></td>
                    <td><?= htmlspecialchars($pupil['class_name']) ?></td>
                    <td><?= htmlspecialchars($pupil['medical_info']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>