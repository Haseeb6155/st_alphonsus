<?php
/*
    PUPIL LIST (DASHBOARD)
    ----------------------
    Displays a list of all students with their class details.
    Restricted to Admins and Teachers.
    Updated: Parents only see their own children.
*/

include '../db.php';

// 1. Session & Role Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? 0; // Grab the ID for filtering

// 2. Fetch Data
// LOGIC: Check role. If parent, filter by their ID. If admin/teacher, show all.

if ($role === 'parent') {
    // --- PARENT VIEW: Show ONLY linked children ---
    // We join Pupils -> Pupil_Parent -> Parents to match the User ID
    $sql = "SELECT Pupils.pupil_id, Pupils.full_name, Classes.class_name, Pupils.medical_info, Pupils.address
            FROM Pupils
            LEFT JOIN Classes ON Pupils.class_id = Classes.class_id
            JOIN Pupil_Parent ON Pupils.pupil_id = Pupil_Parent.pupil_id
            JOIN Parents ON Pupil_Parent.parent_id = Parents.parent_id
            WHERE Parents.user_id = :uid
            ORDER BY Pupils.pupil_id ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $user_id]);

} else {
    // --- STAFF VIEW: Show ALL pupils ---
    $sql = "SELECT Pupils.pupil_id, Pupils.full_name, Classes.class_name, Pupils.medical_info, Pupils.address
            FROM Pupils
            LEFT JOIN Classes ON Pupils.class_id = Classes.class_id
            ORDER BY Pupils.pupil_id ASC";
            
    $stmt = $pdo->query($sql);
}

$pupils = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pupil List - St Alphonsus</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        
        <div class="page-header">
            <h1>Pupil List</h1>
            
            <?php if ($role != 'parent'): ?>
                <a href="add_pupil.php" class="btn btn-primary">
                    + Add New Pupil
                </a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <?php if (count($pupils) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Pupil ID</th>
                            <th>Full Name</th>
                            <th>Class</th>
                            <th>Medical Info</th>
                            <?php if ($role != 'parent'): ?>
                                <th style="text-align: right;">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pupils as $pupil): ?>
                            <tr>
                                <td style="color: var(--text-muted);">
                                    #<?= htmlspecialchars($pupil['pupil_id']) ?>
                                </td>

                                <td style="font-weight: 500;">
                                    <?= htmlspecialchars($pupil['full_name']) ?>
                                </td>

                                <td>
                                    <span class="status-pill status-active">
                                        <?= htmlspecialchars($pupil['class_name'] ?? 'Unassigned') ?>
                                    </span>
                                </td>

                                <td style="color: var(--text-muted);">
                                    <?= htmlspecialchars(substr($pupil['medical_info'], 0, 30)) ?>
                                    <?= strlen($pupil['medical_info']) > 30 ? '...' : '' ?>
                                </td>
                                
                                <?php if ($role != 'parent'): ?>
                                    <td style="text-align: right;">
                                        <a href="edit_pupil.php?id=<?= $pupil['pupil_id'] ?>" 
                                           class="btn btn-sm" 
                                           style="background: #374151; margin-right: 5px;">
                                           Edit
                                        </a>
                                        
                                        <a href="delete_pupil.php?id=<?= $pupil['pupil_id'] ?>" 
                                           class="btn btn-sm" 
                                           style="background: rgba(239,68,68,0.2); color: #f87171;"
                                           onclick="return confirm('Are you sure you want to delete this pupil record?');">
                                           Delete
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                    <p>No pupils found.</p>
                </div>
            <?php endif; ?>
        </div> </div> <?php include '../footer.php'; ?>

</body>
</html>