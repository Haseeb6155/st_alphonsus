<?php
include '../db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $salary = trim($_POST['annual_salary']);

    if (empty($full_name) || empty($phone)) {
        $message = "<div class='status-pill status-inactive'>Name and Phone are required!</div>";
    } else {
        try {
            $sql = "INSERT INTO teachers (full_name, address, phone, annual_salary) 
                    VALUES (:full_name, :address, :phone, :salary)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':full_name' => $full_name, ':address' => $address, ':phone' => $phone, ':salary' => $salary]);
            
            // Success: Redirect to list after 1 second
            $message = "<div class='status-pill status-active'>Success! Teacher added.</div>";
            header("refresh:1;url=teachers.php"); 
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
    <title>Add Teacher</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Add New Teacher</h2>
        <?= $message ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="e.g. Sarah Mitchell">
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="e.g. 07700 900000">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Home address...">
            </div>

            <div class="form-group">
                <label>Annual Salary (£)</label>
                <input type="number" name="annual_salary" min="0" step="0.01" required placeholder="e.g. 35000">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Record</button>
            <a href="teachers.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>

</body>
</html>