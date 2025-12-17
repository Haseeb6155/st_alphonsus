<?php
include '../db.php';

session_start();

// --- SECURITY CHECK ---
// Enforce strict access control: Only administrators are authorized to modify class details.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Validate the presence of the class ID parameter in the URL
if (!isset($_GET['id'])) { 
    header("Location: classes.php"); 
    exit; 
}
$id = $_GET['id'];

// --- DATA RETRIEVAL ---
// Fetch existing class details to pre-populate the form fields
$stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = :id");
$stmt->execute([':id' => $id]);
$class = $stmt->fetch();

// Fetch the list of all teachers to populate the dropdown selection
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll();

// --- HANDLE UPDATE REQUEST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);
    $capacity   = trim($_POST['capacity']);
    
    // Handle optional foreign key: Convert empty selection to NULL for database compatibility
    $teacher_id = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;

    try {
        // Execute prepared statement to update the class record securely
        $sql = "UPDATE classes SET class_name=:name, capacity=:cap, teacher_id=:tid WHERE class_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $class_name, 
            ':cap'  => $capacity, 
            ':tid'  => $teacher_id, 
            ':id'   => $id
        ]);
        
        $message = "<div class='status-pill status-active'>Class Updated!</div>";
        
        // Refresh the local variable with the updated data so the form reflects changes immediately
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = :id");
        $stmt->execute([':id' => $id]);
        $class = $stmt->fetch();
        
    } catch (PDOException $e) {
        // Capture and display database errors (e.g., constraint violations)
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
            <div class="form-group">
                <label>Class Name</label>
                <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>">
            </div>
            
            <div class="form-group">
                <label>Capacity</label>
                <input type="number" name="capacity" value="<?= htmlspecialchars($class['capacity']) ?>">
            </div>
            
            <div class="form-group">
                <label>Teacher</label>
                <select name="teacher_id">
                    <option value="">-- No Teacher --</option>
                    
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