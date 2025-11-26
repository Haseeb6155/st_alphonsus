<?php
include 'db.php';

$message = "";

// 1. Get the Pupil ID from the URL (e.g., ?id=5)
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Kick them back if no ID
    exit;
}

$id = $_GET['id'];

// 2. Fetch the Pupil's Current Data
$sql = "SELECT * FROM Pupils WHERE pupil_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$pupil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pupil) {
    die("Pupil not found!");
}

// 3. Fetch Classes for the Dropdown
$class_stmt = $pdo->query("SELECT * FROM Classes");
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Handle the Update Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    if (empty($full_name) || empty($address)) {
        $message = "<p style='color: red;'>Name and Address are required!</p>";
    } else {
        try {
            // The UPDATE SQL Command
            $sql = "UPDATE Pupils 
                    SET full_name = :full_name, 
                        address = :address, 
                        medical_info = :medical_info, 
                        class_id = :class_id 
                    WHERE pupil_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':medical_info' => $medical_info,
                ':class_id' => $class_id,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Details updated.</p>";
            
            // Refresh the data so the form shows the new info
            $stmt = $pdo->prepare("SELECT * FROM Pupils WHERE pupil_id = :id");
            $stmt->execute([':id' => $id]);
            $pupil = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Edit Pupil</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 400px; margin-top: 20px; }
        label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: orange; color: white; border: none; cursor: pointer; }
        button:hover { background: darkorange; }
        .back-link { display: block; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>Edit Pupil: <?= htmlspecialchars($pupil['full_name']) ?></h1>
    
    <?= $message ?>

    <form method="POST">
        
        <label>Full Name: *</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($pupil['full_name']) ?>">

        <label>Address: *</label>
        <input type="text" name="address" value="<?= htmlspecialchars($pupil['address']) ?>">

        <label>Medical Info:</label>
        <textarea name="medical_info"><?= htmlspecialchars($pupil['medical_info']) ?></textarea>

        <label>Class: *</label>
        <select name="class_id">
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['class_id'] ?>" 
                    <?= $class['class_id'] == $pupil['class_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($class['class_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update Pupil</button>
    </form>

    <a href="index.php" class="back-link">← Back to Pupil List</a>

</body>
</html>