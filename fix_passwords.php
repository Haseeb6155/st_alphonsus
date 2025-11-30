<?php

include 'db.php';

// The Default Credentials we want to create
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
    <title>Fix Passwords</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">

    <div class="form-card" style="text-align: center;">
        <h2 class="mb-4">System Reset</h2>
        
        <div style="text-align: left; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <?php
            foreach ($users as $username => $plain_password) {
                // 1. Hash the password
                $hashed = password_hash($plain_password, PASSWORD_DEFAULT);
                
                try {
                    // 2. Delete old user (to be safe)
                    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$username]);
                    
                    // 3. Insert new user
                    // Note: For this simple system, the 'role' is the same as the username
                    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$username, $hashed, $username]);
                    
                    echo "<div class='status-pill status-active' style='display:block; margin-bottom:10px;'>
                            ✔ Reset <b>$username</b> 
                            <span style='float:right; opacity:0.7'>(Pass: $plain_password)</span>
                          </div>";
                    
                } catch (PDOException $e) {
                    echo "<div class='status-pill status-inactive'>
                            ❌ Error for $username: " . $e->getMessage() . "
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