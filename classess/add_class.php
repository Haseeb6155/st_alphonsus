<?php
include '../db.php';
$message = "";

// Fetch teachers for dropdown
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);
    $capacity = trim($_POST['capacity']);
    $teacher_id = $_POST['teacher_id'];

    if (empty($class_name) || empty($capacity)) {
        $message = "<div class='status-pill status-inactive'>Class Name and Capacity required!</div>";
    } else {
        try {
            $sql = "INSERT INTO classes (class_name, capacity, teacher_id) VALUES (:name, :cap, :tid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $class_name, ':cap' => $capacity, ':tid' => $teacher_id]);
            
            $message = "<div class='status-pill status-active'>Success! Class Created.</div>";
            header("refresh:1;url=classes.php");
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
    <title>Add Class</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Create New Class</h2>
        <?= $message ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Class Name</label>
                <input type="text" name="class_name" placeholder="e.g. Year 5B">
            </div>
            
            <div class="form-group">
                <label>Capacity</label>
                <input type="number" name="capacity" placeholder="e.g. 30">
            </div>

            <div class="form-group">
                <label>Assign Teacher</label>
                <select name="teacher_id">
                    <option value="">-- No Teacher --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['teacher_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Class</button>
            <a href="classes.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>

</body>
</html>