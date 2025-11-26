<?php
include '../db.php';
$message = "";

// 1. Check for ID
if (!isset($_GET['id'])) {
    header("Location: teachers.php");
    exit;
}

$id = $_GET['id'];

// 2. Fetch Current Data (Pre-fill the form)
$sql = "SELECT * FROM teachers WHERE teacher_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) { die("Teacher not found!"); }

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $salary = trim($_POST['annual_salary']);

    if (empty($full_name) || empty($phone)) {
        $message = "<p style='color: red;'>Name and Phone are required!</p>";
    } else {
        try {
            $sql = "UPDATE teachers 
                    SET full_name = :full_name, 
                        address = :address, 
                        phone = :phone, 
                        annual_salary = :salary 
                    WHERE teacher_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':phone' => $phone,
                ':salary' => $salary,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Teacher details updated.</p>";
            
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM teachers WHERE teacher_id = :id");
            $stmt->execute([':id' => $id]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Edit Teacher: <?= htmlspecialchars($teacher['full_name']) ?></h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Full Name: *</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>">

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($teacher['address']) ?>">

        <label>Phone: *</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($teacher['phone']) ?>">

        <label>Annual Salary:</label>
        <input type="text" name="annual_salary" value="<?= htmlspecialchars($teacher['annual_salary']) ?>">

        <button type="submit">Update Teacher</button>
    </form>

    <a href="teachers.php" class="back-link">← Back to Teacher List</a>

</body>
</html>