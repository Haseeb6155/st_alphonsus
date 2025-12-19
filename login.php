<?php
/*
    LOGIN CONTROLLER
    Handles safe authentication and user redirects.
*/

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; 

$error = "";

// Process Login Attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize username input
    $username = trim($_POST['username']); 
    $password = $_POST['password'];

    try {
        // Execute prepared statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :name");
        $stmt->execute([':name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password hash against database record
        if ($user && password_verify($password, $user['password'])) {
            
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            
            // Store user authentication details in session
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Redirect user based on their assigned role
            $redirectPath = ($user['role'] === 'parent') ? 'attendance/attendance.php' : 'Pupils/index.php';
            header("Location: " . $redirectPath);
            exit;

        } else {
            // Use generic error message to prevent username enumeration
            $error = "Invalid username or password.";
        }

    } catch (PDOException $e) {
        // Log actual error internally and show generic message to user
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