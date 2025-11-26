<?php
// Include the database connection setup
include '../db.php';

$message = "";

// 1. Validation: Check if an ID was passed in the URL (e.g., edit_pupil.php?id=5)
if (!isset($_GET['id'])) {
    // If no ID is present, we can't edit anything, so redirect to the main list
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// 2. Fetch Existing Data: Get the pupil's current info to pre-fill the form
// This ensures the user sees what they are editing.
$sql = "SELECT * FROM Pupils WHERE pupil_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$pupil = $stmt->fetch(PDO::FETCH_ASSOC);

// Safety check: Stop if the ID doesn't exist in the database
if (!$pupil) {
    die("Pupil not found!");
}

// 3. Fetch Classes: Get the list for the dropdown menu
$class_stmt = $pdo->query("SELECT * FROM Classes");
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Handle Form Submission: Process the update when the user clicks 'Update Pupil'
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs to remove extra whitespace
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    // Basic validation to prevent saving empty records
    if (empty($full_name) || empty($address)) {
        $message = "<p style='color: red;'>Name and Address are required!</p>";
    } else {
        try {
            // Prepare the SQL UPDATE command
            $sql = "UPDATE Pupils 
                    SET full_name = :full_name, 
                        address = :address, 
                        medical_info = :medical_info, 
                        class_id = :class_id 
                    WHERE pupil_id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            // Execute with bound parameters to prevent SQL injection
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':medical_info' => $medical_info,
                ':class_id' => $class_id,
                ':id' => $id
            ]);

            $message = "<p style='color: green;'>Success! Details updated.</p>";
            
            // Refresh the $pupil variable so the form displays the new changes immediately
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
    <link rel="stylesheet" href="../style.css">
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