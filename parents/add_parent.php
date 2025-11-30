<?php
include '../db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($full_name) || empty($phone)) {
        $message = "<div class='status-pill status-inactive'>Name and Phone required!</div>";
    } else {
        try {
            $sql = "INSERT INTO Parents (full_name, email, phone, address) VALUES (:name, :email, :phone, :addr)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $full_name, ':email' => $email, ':phone' => $phone, ':addr' => $address]);
            
            $message = "<div class='status-pill status-active'>Parent Added Successfully!</div>";
            header("refresh:1;url=parents.php");
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
    <title>Add Parent</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Add New Parent</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name"></div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" pattern="[0-9\s]+" title="Numbers and spaces only" required>
            </div>
            <div class="form-group"><label>Address</label><input type="text" name="address"></div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Record</button>
            <a href="parents.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>