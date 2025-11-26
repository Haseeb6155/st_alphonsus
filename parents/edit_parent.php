<?php
include '../db.php';
$message = "";

// 1. Check for ID
if (!isset($_GET['id'])) {
    header("Location: parents.php");
    exit;
}

$id = $_GET['id'];

// 2. Fetch Current Parent Data to pre-fill the form
$sql = "SELECT * FROM Parents WHERE parent_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) { die("Parent not found!"); }

// 3. Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($full_name) || empty($phone)) {
        $message = "<p style='color: red;'>Name and Phone are required!</p>";
    } else {
        try {
            $sql = "UPDATE Parents 
                    SET full_name = :full_name, 
                        email = :email, 
                        phone = :phone, 
                        address = :address 
                    WHERE parent_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Parent details updated.</p>";
            
            // Refresh Data so the form shows new info immediately
            $stmt = $pdo->prepare("SELECT * FROM Parents WHERE parent_id = :id");
            $stmt->execute([':id' => $id]);
            $parent = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Edit Parent</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        nav { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <h1>Edit Parent: <?= htmlspecialchars($parent['full_name']) ?></h1>
    
    <?= $message ?>

    <form method="POST">
        <label>Full Name: *</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($parent['full_name']) ?>">

        <label>Email:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($parent['email']) ?>">

        <label>Phone: *</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($parent['phone']) ?>">

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($parent['address']) ?>">

        <button type="submit">Update Parent</button>
    </form>

    <a href="parents.php" class="back-link">← Back to Parent List</a>

</body>
</html>