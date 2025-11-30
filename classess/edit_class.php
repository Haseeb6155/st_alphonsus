<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) { header("Location: classes.php"); exit; }
$id = $_GET['id'];

// Fetch Class
$stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = :id");
$stmt->execute([':id' => $id]);
$class = $stmt->fetch();

// Fetch Teachers
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);
    $capacity = trim($_POST['capacity']);
    $teacher_id = $_POST['teacher_id'];

    try {
        $sql = "UPDATE classes SET class_name=:name, capacity=:cap, teacher_id=:tid WHERE class_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name' => $class_name, ':cap' => $capacity, ':tid' => $teacher_id, ':id' => $id]);
        $message = "<div class='status-pill status-active'>Class Updated!</div>";
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = :id");
        $stmt->execute([':id' => $id]);
        $class = $stmt->fetch();
    } catch (PDOException $e) {
        $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Edit Class</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Class Name</label><input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>"></div>
            <div class="form-group"><label>Capacity</label><input type="number" name="capacity" value="<?= htmlspecialchars($class['capacity']) ?>"></div>
            <div class="form-group">
                <label>Teacher</label>
                <select name="teacher_id">
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['teacher_id'] ?>" <?= $t['teacher_id'] == $class['teacher_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Class</button>
            <a href="classes.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>