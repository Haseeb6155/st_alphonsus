<?php
include '../db.php';

// Select all the columns we need for the contact list
$sql = "SELECT parent_id, full_name, email, phone, address FROM Parents";

$stmt = $pdo->query($sql);
$parents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>St Alphonsus Parent Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>St Alphonsus - Parent List</h1>

    <a href="add_parent.php" class="add-btn">+ Add New Parent</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parents as $parent): ?>
                <tr>
                    <td><?= htmlspecialchars($parent['parent_id']) ?></td>
                    <td><?= htmlspecialchars($parent['full_name']) ?></td>
                    <td><?= htmlspecialchars($parent['email']) ?></td>
                    <td><?= htmlspecialchars($parent['phone']) ?></td>
                    <td><?= htmlspecialchars($parent['address']) ?></td>
                    <td>
                        <a href="edit_parent.php?id=<?= $parent['parent_id'] ?>" class="edit-link">Edit</a>
                        |
                        <a href="delete_parent.php?id=<?= $parent['parent_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this parent?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>