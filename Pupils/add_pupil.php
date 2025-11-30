<?php
include '../db.php';
$message = "";

// Fetch Classes for dropdown
$classes = $pdo->query("SELECT * FROM Classes")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    if (empty($full_name) || empty($address) || empty($class_id)) {
        $message = "<div class='status-pill status-inactive'>Name, Address, and Class are required!</div>";
    } else {
        try {
            $sql = "INSERT INTO Pupils (full_name, address, medical_info, class_id) VALUES (:name, :addr, :med, :cid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $full_name, ':addr' => $address, ':med' => $medical_info, ':cid' => $class_id]);
            
            $message = "<div class='status-pill status-active'>Pupil Added Successfully!</div>";
            header("refresh:1;url=index.php");
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
    <title>Add Pupil</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Register New Pupil</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" placeholder="e.g. John Doe"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address" placeholder="Home address..."></div>
            <div class="form-group"><label>Medical Info</label><textarea name="medical_info" placeholder="Allergies, conditions, etc."></textarea></div>
            <div class="form-group">
                <label>Assign to Class</label>
                <select name="class_id">
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Pupil</button>
            <a href="index.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>