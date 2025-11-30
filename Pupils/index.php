<?php
/*
    PUPIL LIST (DASHBOARD)
    ----------------------
    Displays a list of all students with their class details.
    Restricted to Admins and Teachers.
*/

include '../db.php';

// 1. Session & Role Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? 'guest';

// 2. Fetch Data
// We join Pupils with Classes to get the readable Class Name (Year 5A) instead of just an ID (1)
$fetch_pupils_query = "SELECT Pupils.pupil_id, Pupils.full_name, Classes.class_name, Pupils.medical_info, Pupils.address
                       FROM Pupils
                       LEFT JOIN Classes ON Pupils.class_id = Classes.class_id
                       ORDER BY Pupils.pupil_id ASC";

$stmt = $pdo->query($fetch_pupils_query);
$pupils = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pupil List - St Alphonsus</title>
    <!-- Use specific path to style.css. If this file is in /Pupils/, we go up one level ../ -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Include the Navigation Bar -->
    <?php include '../nav.php'; ?>

    <div class="container">
        
        <!-- Page Header with Title and Action Button -->
        <div class="page-header">
            <h1>Pupil List</h1>
            
            <!-- Hide 'Add' button for parents -->
            <?php if ($role != 'parent'): ?>
                <a href="add_pupil.php" class="btn btn-primary">
                    + Add New Pupil
                </a>
            <?php endif; ?>
        </div>

        <!-- The Data Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Pupil ID</th>
                        <th>Full Name</th>
                        <th>Class</th>
                        <th>Medical Info</th>
                        <!-- Only show Actions column to staff -->
                        <?php if ($role != 'parent'): ?>
                            <th style="text-align: right;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pupils as $pupil): ?>
                        <tr>
                            <!-- Pupil ID Column -->
                            <td style="color: var(--text-muted);">
                                #<?= htmlspecialchars($pupil['pupil_id']) ?>
                            </td>

                            <!-- Name Column (Bold) -->
                            <td style="font-weight: 500;">
                                <?= htmlspecialchars($pupil['full_name']) ?>
                            </td>

                            <!-- Class Column -->
                            <td>
                                <span class="status-pill status-active">
                                    <?= htmlspecialchars($pupil['class_name'] ?? 'Unassigned') ?>
                                </span>
                            </td>

                            <!-- Medical Info (Truncated if too long) -->
                            <td style="color: var(--text-muted);">
                                <?= htmlspecialchars(substr($pupil['medical_info'], 0, 30)) ?>
                                <?= strlen($pupil['medical_info']) > 30 ? '...' : '' ?>
                            </td>
                            
                            <!-- Action Buttons -->
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
        </div> <!-- End Table Container -->

    </div> <!-- End Main Container -->

    <?php include '../footer.php'; ?>

</body>
</html>