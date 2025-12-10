<?php
include '../db.php';

// --- THE FIX: Start Session & Define Role ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['role'] ?? 'guest';
// -------------------------------------------

$message = "";

// 1. Fetch Classes for dropdown
$classes = $pdo->query("SELECT * FROM Classes")->fetchAll();
$pupils = [];

// 2. If Class selected, fetch students
$selected_class_id = $_GET['class_id'] ?? null;
$selected_date = $_GET['date'] ?? date('Y-m-d');

if ($selected_class_id) {
    $stmt = $pdo->prepare("SELECT * FROM Pupils WHERE class_id = :cid ORDER BY full_name ASC");
    $stmt->execute([':cid' => $selected_class_id]);
    $pupils = $stmt->fetchAll();
}

// 3. Handle Bulk Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    
    // Safety check: ensure arrays exist to prevent errors if form is empty
    $statuses = $_POST['status'] ?? []; 
    $notes = $_POST['notes'] ?? [];     
    
    if (!empty($statuses)) {
        try {
            $pdo->beginTransaction();
            
            // UPSERT LOGIC (Distinction Level)
            $sql = "INSERT INTO Attendance (pupil_id, attendance_date, status, notes) 
                    VALUES (:pid, :date, :stat, :note)
                    ON DUPLICATE KEY UPDATE status = :stat, notes = :note";
                    
            $stmt = $pdo->prepare($sql);

            foreach ($statuses as $pupil_id => $status) {
                $note_text = trim($notes[$pupil_id] ?? '');
                
                $stmt->execute([
                    ':pid' => $pupil_id,
                    ':date' => $date,
                    ':stat' => $status,
                    ':note' => $note_text
                ]);
            }
            
            $pdo->commit();
            $message = "<div class='status-pill status-active'>Attendance Saved Successfully!</div>";
            
            // Reset to clear form
            $pupils = []; 
            $selected_class_id = null;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Mark Class Attendance</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Class Register</h1>
            <a href="attendance.php" class="btn btn-sm" style="background: transparent; border: 1px solid var(--text-muted);">Back to Log</a>
        </div>

        <?= $message ?>

        <div class="form-card" style="width: 100%; max-width: 100%; margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 20px; align-items: end; box-shadow: none; padding: 0; background: transparent;">
                <div style="flex: 1;">
                    <label>Select Class:</label>
                    <select name="class_id" required>
                        <option value="">-- Choose Class --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>" <?= $c['class_id'] == $selected_class_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Date:</label>
                    <input type="date" name="date" value="<?= $selected_date ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: auto; margin-bottom: 20px;">Load Pupils</button>
            </form>
        </div>

        <?php if (!empty($pupils)): ?>
            <form method="POST">
                <input type="hidden" name="class_id" value="<?= $selected_class_id ?>">
                <input type="hidden" name="date" value="<?= $selected_date ?>">
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Pupil Name</th>
                                <th>Status</th>
                                <th>Notes</th> </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pupils as $p): ?>
                                <tr>
                                    <td style="font-weight:500;"><?= htmlspecialchars($p['full_name']) ?></td>
                                    <td>
                                        <label style="display:inline; margin-right:15px; color: var(--success); cursor:pointer;">
                                            <input type="radio" name="status[<?= $p['pupil_id'] ?>]" value="Present" checked> Present
                                        </label>
                                        <label style="display:inline; margin-right:15px; color: var(--danger); cursor:pointer;">
                                            <input type="radio" name="status[<?= $p['pupil_id'] ?>]" value="Absent"> Absent
                                        </label>
                                        <label style="display:inline; color: var(--warning); cursor:pointer;">
                                            <input type="radio" name="status[<?= $p['pupil_id'] ?>]" value="Late"> Late
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" name="notes[<?= $p['pupil_id'] ?>]" placeholder="e.g. Sick..." style="padding: 5px; font-size: 0.9rem;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Save Register</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>