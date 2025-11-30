<?php
/*
    ATTENDANCE DASHBOARD
    --------------------
    Visualizes attendance records with color-coded status pills.
*/
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['role'] ?? 'guest';

$sql = "SELECT Attendance.*, Pupils.full_name 
        FROM Attendance 
        JOIN Pupils ON Attendance.pupil_id = Pupils.pupil_id 
        ORDER BY attendance_date DESC";
$stmt = $pdo->query($sql);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Log</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Attendance Log</h1>
            <?php if ($role != 'parent'): ?>
                <a href="mark_attendance.php" class="btn btn-primary">+ Mark Attendance</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pupil Name</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): 
                        // Determine color class based on status text
                        $statusClass = 'status-active'; // Default (Green)
                        if ($row['status'] == 'Absent') $statusClass = 'status-inactive'; // Red
                        if ($row['status'] == 'Late') $statusClass = 'status-warning';    // Orange
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['attendance_date']) ?></td>
                            <td style="font-weight: 500;"><?= htmlspecialchars($row['full_name']) ?></td>
                            <td>
                                <span class="status-pill <?= $statusClass ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.9rem;">
                                <?= htmlspecialchars($row['notes']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
                        <?php include '../footer.php'; ?>
</body>
</html>