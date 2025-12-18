<?php
include '../db.php';

// Start the session if it is not already active
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit(); 
}

$role = $_SESSION['role'];
$current_user_id = $_SESSION['user_id'];

// Fetch class list for the dropdown filter (Staff only)
$classes = [];
if ($role != 'parent') {
    $classes = $pdo->query("SELECT * FROM Classes ORDER BY class_name ASC")->fetchAll();
}

// Retrieve filter parameters from URL or set defaults
$filter_class = $_GET['class_id'] ?? '';
$filter_date  = $_GET['attendance_date'] ?? date('Y-m-d'); 

// Base query to fetch attendance data joined with pupil and class info
$sql = "SELECT Attendance.*, Pupils.full_name, Classes.class_name 
        FROM Attendance 
        JOIN Pupils ON Attendance.pupil_id = Pupils.pupil_id 
        LEFT JOIN Classes ON Pupils.class_id = Classes.class_id
        WHERE Attendance.attendance_date = :date";

$params = [':date' => $filter_date];

// Modify query based on user role
if ($role == 'parent') {
    // Restrict parents to only view records for their linked children
    $sql .= " AND Attendance.pupil_id IN (
                SELECT pp.pupil_id 
                FROM Pupil_Parent pp
                JOIN Parents p ON pp.parent_id = p.parent_id
                WHERE p.user_id = :uid
              )";
    $params[':uid'] = $current_user_id;

} else {
    // Apply class filter for staff if a specific class is selected
    if (!empty($filter_class)) {
        $sql .= " AND Classes.class_id = :cid";
        $params[':cid'] = $filter_class;
    }
}

// Sort results by class and pupil name
$sql .= " ORDER BY Classes.class_name ASC, Pupils.full_name ASC";

// Execute the prepared statement
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Log</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .filter-bar {
            background-color: var(--bg-card);
            padding: 20px;
            border-radius: var(--radius);
            border: var(--border);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: end;
        }
        .filter-group { flex: 1; }
        .filter-btn { height: 46px; margin-bottom: 2px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Attendance Log</h1>
            
            <?php 
            // Initiate the if-clause and close this php block
            if ($role != 'parent'): ?>
                <a href="mark_attendance.php" class="btn btn-primary">+ Mark New Register</a>
            <?php endif;// End the if-clause block ?>
        </div>

        <form method="GET" class="filter-bar">
            <div class="filter-group">
                <label>Date:</label>
                <input type="date" name="attendance_date" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            
            <?php 
            // Initiate the if-clause and close this php block
            if ($role != 'parent'): ?>
                <div class="filter-group">
                    <label>Filter by Class:</label>
                    <select name="class_id">
                        <option value="">-- All Classes --</option>
                        <?php 
                        // Initiate the for each loop and close this php block
                        // Everything in the HTML block is going to be looped

                        foreach  ($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>" <?= $c['class_id'] == $filter_class ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['class_name']) ?>
                            </option>
                        <?php endforeach;// Finish the for each block ?>
                    </select>
                </div>
            <?php endif;// End the if-clause block ?>

            <button type="submit" class="btn btn-primary filter-btn">View Records</button>
            
            <?php if ($role != 'parent'): ?>
                <a href="attendance.php" class="btn btn-sm" style="background: transparent; border: 1px solid var(--text-muted); height: 46px; line-height: 32px; margin-bottom: 2px;">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-container">
            <?php if (count($records) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Pupil Name</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <?php if ($role != 'parent'): ?>
                                <th style="text-align:right;">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $row): 
                            // Determine CSS class based on attendance status
                            $statusClass = 'status-active'; 
                            if ($row['status'] == 'Absent') $statusClass = 'status-inactive'; 
                            if ($row['status'] == 'Late') $statusClass = 'status-warning';    
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['attendance_date']) ?></td>
                                <td style="color:var(--text-muted); font-size:0.9rem; font-weight:bold;">
                                    <?= htmlspecialchars($row['class_name']) ?>
                                </td>
                                <td style="font-weight: 500;"><?= htmlspecialchars($row['full_name']) ?></td>
                                <td>
                                    <span class="status-pill <?= $statusClass ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.9rem;">
                                    <?= htmlspecialchars($row['notes']) ?>
                                </td>
                                
                                <?php if ($role != 'parent'): ?>
                                    <td style="text-align: right;">
                                        <a href="delete_attendance.php?id=<?= $row['attendance_id'] ?>" 
                                           style="color: var(--danger); font-size: 0.85rem; font-weight:bold;"
                                           onclick="return confirm('Delete this record?');">
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
                    <h3>No records found.</h3>
                    <?php if ($role == 'parent'): ?>
                        <p>No attendance records found for your linked children on this date.</p>
                    <?php else: ?>
                        <p>Try selecting a different date or marking new attendance.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>