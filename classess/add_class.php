<?php
include '../db.php';

$message = "";

// 1. Fetch Teachers for the Dropdown
// We need the ID and Name so we can pick "Mr. Smith" but save "1".
$teacher_sql = "SELECT * FROM teachers";
$teacher_stmt = $pdo->query($teacher_sql);
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);
    $capacity = trim($_POST['capacity']);
    $teacher_id = $_POST['teacher_id'];

    if (empty($class_name) || empty($capacity) || empty($teacher_id)) {
        $message = "<p style='color: red;'>All fields are required!</p>";
    } else {
        try {
            // 3. Insert the new Class
            $sql = "INSERT INTO classes (class_name, capacity, teacher_id) 
                    VALUES (:class_name, :capacity, :teacher_id)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':class_name' => $class_name,
                ':capacity' => $capacity,
                ':teacher_id' => $teacher_id
            ]);

            $message = "<p style='color: green;'>Success! New class added.</p>";
            
        } catch (PDOException $e) {
            // 4. Handle "Duplicate Teacher" Error
            // Error code 23000 usually means a "Unique Constraint" violation
            if ($e->getCode() == 23000) {
                 $message = "<p style='color: red;'>Error: That teacher is already assigned to another class!</p>";
            } else {
                 $message = "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Class</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Add New Class</h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Class Name: *</label>
        <input type="text" name="class_name" placeholder="e.g. Year Three">

        <label>Capacity: *</label>
        <input type="text" name="capacity" placeholder="e.g. 30">

        <label>Assign Teacher: *</label>
        <select name="teacher_id">
            <option value="">-- Select a Teacher --</option>
            <?php foreach ($teachers as $teacher): ?>
                <option value="<?= $teacher['teacher_id'] ?>">
                    <?= htmlspecialchars($teacher['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add Class</button>
    </form>

    <a href="classes.php" class="back-link">← Back to Class List</a>

</body>
</html>