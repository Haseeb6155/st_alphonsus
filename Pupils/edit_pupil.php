<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

// Fetch Pupil
$stmt = $pdo->prepare("SELECT * FROM Pupils WHERE pupil_id = :id");
$stmt->execute([':id' => $id]);
$pupil = $stmt->fetch();

// Fetch Classes
$classes = $pdo->query("SELECT * FROM Classes")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    if (empty($full_name) || empty($address)) {
        $message = "<div class='status-pill status-inactive'>Name and Address required!</div>";
    } else {
        try {
            $sql = "UPDATE Pupils SET full_name=:name, address=:addr, medical_info=:med, class_id=:cid WHERE pupil_id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $full_name, ':addr' => $address, ':med' => $medical_info, ':cid' => $class_id, ':id' => $id]);
            $message = "<div class='status-pill status-active'>Pupil Updated!</div>";
            
            // Refresh
            $stmt = $pdo->prepare("SELECT * FROM Pupils WHERE pupil_id = :id");
            $stmt->execute([':id' => $id]);
            $pupil = $stmt->fetch();
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
    <title>Edit Pupil</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Edit Pupil Record</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($pupil['full_name']) ?>"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address" value="<?= htmlspecialchars($pupil['address']) ?>"></div>
            <div class="form-group"><label>Medical Info</label><textarea name="medical_info"><?= htmlspecialchars($pupil['medical_info']) ?></textarea></div>
            <div class="form-group">
                <label>Class</label>
                <select name="class_id">
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>" <?= $c['class_id'] == $pupil['class_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Pupil</button>
            <a href="index.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>