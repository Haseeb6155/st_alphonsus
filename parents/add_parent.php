<?php
include '../db.php';

session_start();

// --- SECURITY CHECK ---
// Restrict access: Only administrators and teachers are authorized to create parent accounts.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'parent') {
    header("Location: ../index.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $message = "<div class='status-pill status-inactive'>Invalid Email</div>"; 
    }
    else {
        try {
            // Initiate a database transaction to ensure atomicity
            // This ensures both the User login and Parent profile are created together, or not at all.
            $pdo->beginTransaction(); 

            // 1. Create the Authentication Record
            // Hash the password securely before storage
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql_user = "INSERT INTO users (username, password, role) VALUES (:user, :pass, 'parent')";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute([':user' => $username, ':pass' => $hashed]);
            
            // Retrieve the auto-generated ID from the 'users' table to link the parent profile
            $new_user_id = $pdo->lastInsertId(); 

            // 2. Create the Parent Profile Linked to the User ID
            $sql_parent = "INSERT INTO Parents (full_name, email, phone, address, user_id) 
                           VALUES (:name, :email, :phone, :addr, :uid)";
            $stmt = $pdo->prepare($sql_parent);
            $stmt->execute([
                ':name'  => $full_name, 
                ':email' => $email, 
                ':phone' => $phone, 
                ':addr'  => $address,
                ':uid'   => $new_user_id
            ]);

            // Commit the transaction to save all changes
            $pdo->commit(); 
            $message = "<div class='status-pill status-active'>Success! Parent Account Created.</div>";
            header("refresh:2;url=parents.php");

        } catch (PDOException $e) {
            // Rollback the transaction on failure to maintain database integrity
            $pdo->rollBack(); 
            
            // Handle unique constraint violations (e.g., Duplicate Username)
            if ($e->getCode() == 23000) {
                 $message = "<div class='status-pill status-inactive'>Error: Username already exists!</div>";
            } else {
                $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Add Parent</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Create Parent Account</h2>
        <?= $message ?>
        
        <form method="POST">
            <h4 style="color:var(--primary); margin-bottom:10px; border-bottom:1px solid #333; padding-bottom:5px;">Login Credentials</h4>
            
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required placeholder="e.g. parent01">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required placeholder="Secure password">
            </div>

            <h4 style="color:var(--primary); margin-top:20px; margin-bottom:10px; border-bottom:1px solid #333; padding-bottom:5px;">Parent Details</h4>
            
            <div class="form-group"><label>Full Name *</label><input type="text" name="full_name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address"></div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
            <a href="parents.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>