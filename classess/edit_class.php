<?php
include '../db.php';
$message = "";

// 1. Check for ID
if (!isset($_GET['id'])) {
    header("Location: classes.php");
    exit;
}

$id = $_GET['id'];

// 2. Fetch Current Class Data
$sql = "SELECT * FROM classes WHERE class_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) { die("Class not found!"); }

// 3. Fetch Teachers for the Dropdown
$teacher_stmt = $pdo->query("SELECT * FROM teachers");
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);
    $capacity = trim($_POST['capacity']);
    $teacher_id = $_POST['teacher_id'];

    if (empty($class_name) || empty($capacity) || empty($teacher_id)) {
        $message = "<p style='color: red;'>All fields are required!</p>";
    } else {
        try {
            $sql = "UPDATE classes 
                    SET class_name = :class_name, 
                        capacity = :capacity, 
                        teacher_id = :teacher_id 
                    WHERE class_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':class_name' => $class_name,
                ':capacity' => $capacity,
                ':teacher_id' => $teacher_id,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Class details updated.</p>";
            
            // Refresh Data
            $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = :id");
            $stmt->execute([':id' => $id]);
            $class = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Handle duplicate teacher assignment
            if ($e->getCode() == 23000) {
                 $message = "<p style='color: red;'>Error: That teacher is already assigned to another class!</p>";
            } else {
                 $message = "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Edit Class: <?= htmlspecialchars($class['class_name']) ?></h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Class Name: *</label>
        <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>">

        <label>Capacity: *</label>
        <input type="text" name="capacity" value="<?= htmlspecialchars($class['capacity']) ?>">

        <label>Assign Teacher: *</label>
        <select name="teacher_id">
            <?php foreach ($teachers as $teacher): ?>
                <option value="<?= $teacher['teacher_id'] ?>" 
                    <?= $teacher['teacher_id'] == $class['teacher_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($teacher['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update Class</button>
    </form>

    <a href="classes.php" class="back-link">← Back to Class List</a>

</body>
</html>