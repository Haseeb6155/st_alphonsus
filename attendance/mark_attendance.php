<?php
include '../db.php';
$message = "";

// Fetch Pupils for dropdown
$pupils = $pdo->query("SELECT pupil_id, full_name FROM Pupils ORDER BY full_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pupil_id = $_POST['pupil_id'];
    $date = $_POST['attendance_date'];
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);

    if (empty($pupil_id) || empty($date)) {
        $message = "<div class='status-pill status-inactive'>Pupil and Date are required!</div>";
    } else {
        try {
            $sql = "INSERT INTO Attendance (pupil_id, attendance_date, status, notes) 
                    VALUES (:pid, :date, :status, :notes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':pid' => $pupil_id, ':date' => $date, ':status' => $status, ':notes' => $notes]);

            $message = "<div class='status-pill status-active'>Attendance Recorded.</div>";
            header("refresh:1;url=attendance.php");
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Mark Attendance</h2>
        <?= $message ?>

        <form method="POST">
            <div class="form-group">
                <label>Select Pupil</label>
                <select name="pupil_id">
                    <option value="">-- Choose Pupil --</option>
                    <?php foreach ($pupils as $p): ?>
                        <option value="<?= $p['pupil_id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="attendance_date" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" placeholder="Reason for absence..." rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Record</button>
            <a href="attendance.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>

</body>
</html>