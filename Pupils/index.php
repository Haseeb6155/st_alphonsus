<?php
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Verify authentication: Redirect unauthenticated users to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Fetch Classes for the dropdown filter (Staff access only)
$classes = [];
if ($role != 'parent') {
    $classes = $pdo->query("SELECT * FROM Classes ORDER BY class_name ASC")->fetchAll();
}

// Initialize pupil data array
$pupils = [];

if ($role == 'parent') {
    // Parent View: Fetch only children linked to the specific logged-in user
    $sql = "SELECT Pupils.*, Classes.class_name 
            FROM Pupils
            LEFT JOIN Classes ON Pupils.class_id = Classes.class_id
            JOIN Pupil_Parent pp ON Pupils.pupil_id = pp.pupil_id
            JOIN Parents p ON pp.parent_id = p.parent_id
            WHERE p.user_id = :uid";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $user_id]);
    $pupils = $stmt->fetchAll();

} else {
    // Staff View: Fetch all pupils with optional class filtering
    $class_filter = $_GET['class_id'] ?? '';
    $params = [];

    $sql = "SELECT Pupils.*, Classes.class_name 
            FROM Pupils 
            LEFT JOIN Classes ON Pupils.class_id = Classes.class_id";

    // Apply filter if a specific class is selected
    if (!empty($class_filter)) {
        $sql .= " WHERE Pupils.class_id = :cid";
        $params[':cid'] = $class_filter;
    }

    $sql .= " ORDER BY Classes.class_name ASC, Pupils.full_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pupils = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pupil List</title>
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
            <h1><?= ($role == 'parent') ? 'My Children' : 'Pupil List' ?></h1>
            
            <?php if ($role != 'parent'): ?>
                <a href="add_pupil.php" class="btn btn-primary">+ Add New Pupil</a>
            <?php endif; ?>
        </div>

        <?php if ($role != 'parent'): ?>
            <form method="GET" class="filter-bar">
                <div class="filter-group">
                    <label>Filter by Class:</label>
                    <select name="class_id">
                        <option value="">-- All Classes --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>" <?= (isset($class_filter) && $c['class_id'] == $class_filter) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary filter-btn">Filter</button>
                
                <?php if (!empty($class_filter)): ?>
                    <a href="index.php" class="btn btn-sm" style="background: transparent; border: 1px solid var(--text-muted); height: 46px; line-height: 32px; margin-bottom: 2px;">Show All</a>
                <?php endif; ?>
            </form>
        <?php endif; ?>

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
                                <td style="color: var(--text-muted);">#<?= htmlspecialchars($pupil['pupil_id']) ?></td>
                                <td style="font-weight: 500;"><?= htmlspecialchars($pupil['full_name']) ?></td>
                                
                                <td>
                                    <span class="status-pill status-active" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        <?= htmlspecialchars($pupil['class_name'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                
                                <td style="color: var(--text-muted); font-size: 0.9rem;">
                                    <?= htmlspecialchars($pupil['medical_info']) ?>
                                </td>
                                
                                <?php if ($role != 'parent'): ?>
                                    <td style="text-align: right;">
                                        <a href="edit_pupil.php?id=<?= $pupil['pupil_id'] ?>" class="btn btn-sm" style="background-color: var(--bg-dark); border: 1px solid var(--border);">Edit</a>
                                        
                                        <a href="delete_pupil.php?id=<?= $pupil['pupil_id'] ?>" 
                                           class="btn btn-sm" 
                                           style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2);"
                                           onclick="return confirm('Are you sure you want to delete this pupil?');">
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
                    <h3>No pupils found.</h3>
                    <?php if ($role == 'parent'): ?>
                        <p>No children are linked to your account yet.</p>
                    <?php else: ?>
                        <p>Try changing the filter or add a new pupil.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>

</body>
</html>