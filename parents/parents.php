<?php
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'parent') {
    die("Access Denied");
}

$role = $_SESSION['role'];
$is_admin = ($role == 'admin');

// 2. Fetch Parents AND their linked Children
// We use a special SQL trick (GROUP_CONCAT) to list all kids in one box.
$sql = "SELECT Parents.*, 
        GROUP_CONCAT(Pupils.full_name SEPARATOR ', ') as children_names
        FROM Parents
        LEFT JOIN Pupil_Parent ON Parents.parent_id = Pupil_Parent.parent_id
        LEFT JOIN Pupils ON Pupil_Parent.pupil_id = Pupils.pupil_id
        GROUP BY Parents.parent_id
        ORDER BY Parents.full_name ASC";

$parents = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Parent Records</h1>
            <?php if ($is_admin): ?>
                <div>
                    <a href="link_parent.php" class="btn btn-sm" style="background: var(--warning); color: #fff; margin-right: 10px;">Link to Pupil</a>
                    <a href="add_parent.php" class="btn btn-primary">+ Add Parent</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Linked Student(s)</th> <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <?php if ($is_admin): ?>
                            <th style="text-align: right;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parents as $p): ?>
                        <tr>
                            <td style="font-weight: 500;"><?= htmlspecialchars($p['full_name']) ?></td>

                            <td>
                                <?php if (!empty($p['children_names'])): ?>
                                    <span class="status-pill status-active">
                                        <?= htmlspecialchars($p['children_names']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #666; font-style: italic;">No child linked</span>
                                <?php endif; ?>
                            </td>

                            <td style="color: var(--text-muted);"><?= htmlspecialchars($p['email']) ?></td>
                            <td><?= htmlspecialchars($p['phone']) ?></td>
                            
                            <td style="color: var(--text-muted);">
                                <?= htmlspecialchars(substr($p['address'], 0, 15)) ?>...
                            </td>
                            
                            <?php if ($is_admin): ?>
                                <td style="text-align: right;">
                                    <a href="edit_parent.php?id=<?= $p['parent_id'] ?>" class="btn btn-sm" style="background: #374151;">Edit</a>
                                    <a href="delete_parent.php?id=<?= $p['parent_id'] ?>" 
                                       class="btn btn-sm" 
                                       style="background: rgba(239,68,68,0.2); color: #f87171;" 
                                       onclick="return confirm('Delete this parent?');">Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>