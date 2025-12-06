<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}
include '../db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Login Credentials
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // 2. Teacher Profile Details
    $full_name = trim($_POST['full_name']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $salary    = trim($_POST['annual_salary']);

    if (empty($username) || empty($password) || empty($full_name)) {
        $message = "<div class='status-pill status-inactive'>Username, Password and Name required!</div>";
    } else {
        try {
            $pdo->beginTransaction(); // Start "All or Nothing"

            // A. Create User Login (Role = teacher)
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql_user = "INSERT INTO users (username, password, role) VALUES (:user, :pass, 'teacher')";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute([':user' => $username, ':pass' => $hashed]);
            
            // Get the new ID from the users table
            $new_user_id = $pdo->lastInsertId();

            // B. Create Teacher Profile (Linked via user_id)
            $sql_teacher = "INSERT INTO teachers (full_name, address, phone, annual_salary, user_id) 
                            VALUES (:name, :addr, :phone, :sal, :uid)";
            $stmt = $pdo->prepare($sql_teacher);
            $stmt->execute([
                ':name'  => $full_name, 
                ':addr'  => $address, 
                ':phone' => $phone, 
                ':sal'   => $salary,
                ':uid'   => $new_user_id
            ]);

            $pdo->commit(); // Save both changes
            $message = "<div class='status-pill status-active'>Success! Teacher Account Created.</div>";
            header("refresh:2;url=teachers.php");

        } catch (PDOException $e) {
            $pdo->rollBack(); // Undo if error
            if ($e->getCode() == 23000) {
                $message = "<div class='status-pill status-inactive'>Username already exists!</div>";
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
    <meta charset="UTF-8"><title>Add Teacher</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Create Teacher Account</h2>
        <?= $message ?>
        <form method="POST">
            <h4 style="color:var(--primary); margin-bottom:10px; border-bottom:1px solid #333; padding-bottom:5px;">Login Credentials</h4>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required placeholder="e.g. mr_smith">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required placeholder="Secure password">
            </div>

            <h4 style="color:var(--primary); margin-top:20px; margin-bottom:10px; border-bottom:1px solid #333; padding-bottom:5px;">Staff Details</h4>
            <div class="form-group"><label>Full Name *</label><input type="text" name="full_name" required></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address"></div>
            <div class="form-group"><label>Annual Salary (£)</label><input type="number" name="annual_salary"></div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
            <a href="teachers.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>