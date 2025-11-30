<?php
/*
    LOGIN LOGIC
    -----------
    Handles user authentication.
*/
session_start();
include 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $selected_role_user = $_POST['user_role']; 
    $password = $_POST['password'];

    // Query: Check if this user exists in our 'users' table
    $query = "SELECT * FROM users WHERE username = :name";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':name' => $selected_role_user]);
    $user = $stmt->fetch();

    // Verify: Does password match the hash?
    if ($user && password_verify($password, $user['password'])) {
        // Success: Set Session Variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect logic based on role
        if ($user['role'] == 'parent') {
            header("Location: libraryy/library.php");
        } else {
            // Default landing page for Staff/Admin
            header("Location: Pupils/index.php"); 
        }
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - St Alphonsus</title>
    <!-- Link to our new Design System -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h1 class="text-center mb-4">St Alphonsus Portal</h1>
        <p class="text-center" style="color: var(--text-muted); margin-bottom: 30px;">
            Please sign in to continue
        </p>
        
        <?php if($error): ?>
            <div style="background: rgba(239,68,68,0.2); color: #fca5a5; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Select Role</label>
                <select name="user_role">
                    <option value="admin">School Administrator</option>
                    <option value="teacher">Teacher</option>
                    <option value="parent">Parent / Guardian</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your secure password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                Sign In
            </button>
        </form>
    </div>

</body>
</html>