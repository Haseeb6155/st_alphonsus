<?php
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $phone     = trim($_POST['phone']);
    $email     = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($full_name)) {
        $message = "<div class='status-pill status-inactive'>All fields required!</div>";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Create Login User
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql_user = "INSERT INTO users (username, password, role) VALUES (:user, :pass, 'parent')";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute([':user' => $username, ':pass' => $hashed]);
            
            $new_user_id = $pdo->lastInsertId();

            // 2. Create Parent Profile (Linked)
            $sql_parent = "INSERT INTO Parents (full_name, phone, email, user_id) VALUES (:name, :phone, :email, :uid)";
            $stmt = $pdo->prepare($sql_parent);
            $stmt->execute([
                ':name' => $full_name, 
                ':phone' => $phone, 
                ':email' => $email,
                ':uid' => $new_user_id
            ]);

            $pdo->commit();
            $message = "<div class='status-pill status-active'>Account Created! <a href='login.php'>Login Now</a></div>";

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                $message = "<div class='status-pill status-inactive'>Username already taken!</div>";
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
    <meta charset="UTF-8"><title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Parent Registration</h2>
        <?= $message ?>
        <form method="POST">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
            <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            <a href="login.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Back to Login</a>
        </form>
    </div>
</body>
</html>