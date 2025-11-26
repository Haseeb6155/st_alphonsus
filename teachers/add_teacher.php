<?php
include '../db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $salary = trim($_POST['annual_salary']);

    if (empty($full_name) || empty($phone)) {
        $message = "<p style='color: red;'>Name and Phone are required!</p>";
    } else {
        try {
            $sql = "INSERT INTO teachers (full_name, address, phone, annual_salary) 
                    VALUES (:full_name, :address, :phone, :salary)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':phone' => $phone,
                ':salary' => $salary
            ]);

            $message = "<p style='color: green;'>Success! New teacher added.</p>";
            
        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Teacher</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Add New Teacher</h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Full Name: *</label>
        <input type="text" name="full_name">

        <label>Address:</label>
        <input type="text" name="address">

        <label>Phone Number: *</label>
        <input type="text" name="phone">

        <label>Annual Salary:</label>
        <input type="text" name="annual_salary">

        <button type="submit">Add Teacher</button>
    </form>

    <a href="teachers.php" class="back-link">← Back to Teacher List</a>

</body>
</html>