<?php
session_start();

// Verify authentication: Only administrators can modify staff records
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit(); 
}

include '../db.php';
$message = "";

// Ensure a valid teacher ID is provided via URL
if (!isset($_GET['id'])) { 
    header("Location: teachers.php"); 
    exit; 
}

$id = $_GET['id'];

// Retrieve current teacher data to pre-fill the form
$stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = :id");
$stmt->execute([':id' => $id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    die("Error: Teacher not found.");
}

// Handle form submission to update the record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address   = trim($_POST['address']);
    $phone     = trim($_POST['phone']);
    $salary    = trim($_POST['annual_salary']);

    try {
        // Execute secure update query
        $sql = "UPDATE teachers SET full_name=:name, address=:addr, phone=:phone, annual_salary=:sal WHERE teacher_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'  => $full_name, 
            ':addr'  => $address, 
            ':phone' => $phone, 
            ':sal'   => $salary, 
            ':id'    => $id
        ]);
        
        $message = "<div class='status-pill status-active'>Teacher Updated Successfully!</div>";
        
        // Refresh data to reflect changes immediately
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = :id"); 
        $stmt->execute([':id' => $id]);
        $teacher = $stmt->fetch();

    } catch (PDOException $e) {
        $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Edit Teacher</h2>
        <?= $message ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($teacher['phone']) ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($teacher['address']) ?>">
            </div>
            <div class="form-group">
                <label>Annual Salary (£)</label>
                <input type="number" name="annual_salary" value="<?= htmlspecialchars($teacher['annual_salary']) ?>">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Teacher</button>
            <a href="teachers.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>