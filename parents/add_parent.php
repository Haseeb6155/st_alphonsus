<?php
include '../db.php';

$message = "";

// 1. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // 2. Validation
    if (empty($full_name) || empty($phone)) {
        $message = "<p style='color: red;'>Name and Phone are required!</p>";
    } else {
        try {
            // 3. Insert the new Parent
            $sql = "INSERT INTO Parents (full_name, email, phone, address) 
                    VALUES (:full_name, :email, :phone, :address)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address
            ]);

            $message = "<p style='color: green;'>Success! New parent added.</p>";
            
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
    <title>Add New Parent</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Add New Parent</h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Full Name: *</label>
        <input type="text" name="full_name">

        <label>Email:</label>
        <input type="text" name="email">

        <label>Phone Number: *</label>
        <input type="text" name="phone">

        <label>Address:</label>
        <input type="text" name="address">

        <button type="submit">Add Parent</button>
    </form>

    <a href="parents.php" class="back-link">← Back to Parent List</a>

</body>
</html>