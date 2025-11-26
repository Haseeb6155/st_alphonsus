<?php
include 'db.php';

$message = ""; // To store success or error messages

// 1. Fetch Classes for the Dropdown (Crucial for "Relationships" marks)
// We need the user to pick a real class, not just type "Year 100".
$class_sql = "SELECT * FROM Classes";
$class_stmt = $pdo->query($class_sql);
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Handle the Form Submission (When they click "Add Pupil")
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Collect data from the form
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    // 3. Validation (Crucial for "Clean Data" marks - 70%+)
    // Check if fields are empty
    if (empty($full_name) || empty($address) || empty($class_id)) {
        $message = "<p style='color: red;'>Error: Name, Address, and Class are required!</p>";
    } else {
        // 4. Safe Insertion (Prevents SQL Injection)
        try {
            $sql = "INSERT INTO Pupils (full_name, address, medical_info, class_id) 
                    VALUES (:full_name, :address, :medical_info, :class_id)";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind the "clean" data to the SQL command
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':medical_info' => $medical_info,
                ':class_id' => $class_id
            ]);

            $message = "<p style='color: green;'>Success! New pupil added.</p>";
            
        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Pupil</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 400px; margin-top: 20px; }
        label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .back-link { display: block; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>Add a New Pupil</h1>
    
    <?= $message ?>

    <form method="POST" action="add_pupil.php">
        
        <label>Full Name: *</label>
        <input type="text" name="full_name">

        <label>Address: *</label>
        <input type="text" name="address">

        <label>Medical Info (Optional):</label>
        <textarea name="medical_info"></textarea>

        <label>Assign to Class: *</label>
        <select name="class_id">
            <option value="">-- Select a Class --</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['class_id'] ?>">
                    <?= htmlspecialchars($class['class_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add Pupil</button>
    </form>

    <a href="index.php" class="back-link">← Back to Pupil List</a>

</body>
</html>