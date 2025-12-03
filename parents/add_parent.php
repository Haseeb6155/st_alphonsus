<?php
require_once '../db.php'; // Use require_once for critical dependencies

$message = "";

// Initialize variables to empty strings (for sticky form)
$full_name = "";
$email = "";
$phone = "";
$address = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);

    // Server-side Validation
    if (empty($full_name) || empty($phone)) {
        $message = "<div class='status-pill status-inactive'>Name and Phone are required!</div>";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='status-pill status-inactive'>Invalid email format!</div>";
    } 
    else {
        try {
            $sql = "INSERT INTO Parents (full_name, email, phone, address) 
                    VALUES (:name, :email, :phone, :addr)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'  => $full_name, 
                ':email' => $email, 
                ':phone' => $phone, 
                ':addr'  => $address
            ]);
            
            $message = "<div class='status-pill status-active'>Parent Added Successfully! Redirecting...</div>";
            
            // Clear variables after success so the form is empty
            $full_name = $email = $phone = $address = "";
            
            header("refresh:2;url=parents.php");
            
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Parent</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Add New Parent</h2>
        
        <?= $message ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       value="<?= htmlspecialchars($full_name) ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($email) ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="<?= htmlspecialchars($phone) ?>" 
                       pattern="[0-9\s]+" 
                       title="Numbers and spaces only" 
                       required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" 
                       id="address" 
                       name="address" 
                       value="<?= htmlspecialchars($address) ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Record</button>
            <a href="parents.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>

</body>
</html>