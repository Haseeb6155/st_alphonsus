<?php
/*
    LOGIN CONTROLLER
    ----------------
    Handles safe authentication and user redirects.
*/

// 1. Start Session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; 

$error = "";

// 2. Process Login Attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Clean input
    $username = trim($_POST['username']); 
    $password = $_POST['password'];

    try {
        // Secure lookup using Prepared Statements (Stops SQL Injection)
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :name");
        $stmt->execute([':name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify hash against the database
        if ($user && password_verify($password, $user['password'])) {
            
            // SECURITY: Refresh ID to kill any hijacked sessions
            session_regenerate_id(true);
            
            // Store user info
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Route based on job title
            $redirectPath = ($user['role'] === 'parent') ? 'attendance/attendance.php' : 'Pupils/index.php';
            header("Location: " . $redirectPath);
            exit;

        } else {
            // Keep error vague so hackers can't guess valid usernames
            $error = "Invalid username or password.";
        }

    } catch (PDOException $e) {
        // Log the real error on the server, show a generic one to the user
        error_log("Login Error: " . $e->getMessage());
        $error = "System error. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - St Alphonsus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h1 class="text-center mb-4">St Alphonsus Login</h1>
        
        <?php if (!empty($error)): ?>
            <div class="status-pill status-inactive" style="display:block; text-align:center; margin-bottom:20px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       placeholder="Enter username..." 
                       autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Enter password..." 
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
            
        </form>
    </div>

</body>
</html>