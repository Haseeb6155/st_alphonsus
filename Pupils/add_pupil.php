<?php
include '../db.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Restrict access to staff and administrators only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'parent') {
    header("Location: ../index.php"); 
    exit;
}

$message = "";
$classes = $pdo->query("SELECT * FROM Classes")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Retrieve and sanitize form input
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    if (empty($full_name) || empty($address) || empty($class_id)) {
        $message = "<div class='status-pill status-inactive'>Name, Address, and Class are required!</div>";
    } else {
        try {
            // Check current class enrollment against capacity
            $check_sql = "SELECT c.capacity, 
                          (SELECT COUNT(*) FROM Pupils p WHERE p.class_id = c.class_id) as current_count
                          FROM Classes c WHERE c.class_id = :cid";
                          
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([':cid' => $class_id]);
            $class_data = $stmt->fetch();

            // Prevent insertion if class capacity is reached
            if ($class_data && $class_data['current_count'] >= $class_data['capacity']) {
                $message = "<div class='status-pill status-inactive'>
                                Action Failed: This class is full! 
                                ({$class_data['current_count']}/{$class_data['capacity']})
                            </div>";
            } else {
                // Capacity available: Insert new pupil record
                $sql = "INSERT INTO Pupils (full_name, address, medical_info, class_id) 
                        VALUES (:name, :addr, :med, :cid)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $full_name, 
                    ':addr' => $address, 
                    ':med' => $medical_info, 
                    ':cid' => $class_id
                ]);
                
                $message = "<div class='status-pill status-active'>Pupil Added Successfully!</div>";
                header("refresh:1;url=index.php");
            }
            
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pupil</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">
    <div class="form-card">
        <h2 class="mb-4">Register New Pupil</h2>
        
        <?= $message ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="e.g. John Doe">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Home address...">
            </div>
            
            <div class="form-group">
                <label>Medical Info</label>
                <textarea name="medical_info" placeholder="Allergies, conditions, etc."></textarea>
            </div>
            
            <div class="form-group">
                <label>Assign to Class</label>
                <select name="class_id">
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>">
                            <?= htmlspecialchars($c['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Pupil</button>
            
            <a href="index.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 10px; background: transparent;">Cancel</a>
        </form>
    </div>
</body>
</html>