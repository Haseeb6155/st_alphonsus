<?php
include 'db.php';
session_start();

// --- SECURITY CHECK ---
// Only allow a logged-in ADMIN to reset the system.
// Everyone else gets blocked immediately.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("ACCESS DENIED: You do not have permission to reset the system.");
}

// ... rest of your code ...
$users = [
    'admin'   => 'admin123',
];
?>

<?php
include 'db.php';

// Define default credentials for system initialization
$users = [
    'admin'   => 'admin123',
    'teacher' => 'teacher123',
    'parent'  => 'parent123'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Reset</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">

    <div class="form-card" style="text-align: center;">
        <h2 class="mb-4">System Reset</h2>
        
        <div style="text-align: left; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <?php
            foreach ($users as $username => $plain_password) {
                // Generate a secure hash for the password
                $hashed = password_hash($plain_password, PASSWORD_DEFAULT);
                
                try {
                    // Remove existing user records to prevent duplicate key errors
                    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$username]);
                    
                    // Insert new user record with hashed password
                    // Role is assigned based on the username for this demonstration
                    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$username, $hashed, $username]);
                    
                    echo "<div class='status-pill status-active' style='display:block; margin-bottom:10px;'>
                            ✔ Reset <b>$username</b> 
                            <span style='float:right; opacity:0.7'>(Pass: $plain_password)</span>
                          </div>";
                    
                } catch (PDOException $e) {
                    echo "<div class='status-pill status-inactive'>
                             Error for $username: " . $e->getMessage() . "
                          </div>";
                }
            }
            ?>
        </div>

        <p style="color: var(--text-muted);">
            Database updated successfully.<br>
            You can now log in with these credentials.
        </p>
        
        <a href="login.php" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Go to Login Page</a>
    </div>

</body>
</html>