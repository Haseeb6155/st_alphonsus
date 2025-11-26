<?php
// Include the database connection configuration
include '../db.php';

$message = ""; 

// 1. Fetch Classes: We need to populate the dropdown menu first.
// This ensures users can only assign pupils to valid, existing classes.
$class_sql = "SELECT * FROM Classes";
$class_stmt = $pdo->query($class_sql);
$classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Handle Form Submission: Check if the user has clicked "Add Pupil"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Collect and sanitize the input data
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $medical_info = trim($_POST['medical_info']);
    $class_id = $_POST['class_id'];

    // 3. Validation: Ensure all mandatory fields are present.
    // This protects the database from incomplete records.
    if (empty($full_name) || empty($address) || empty($class_id)) {
        $message = "<p style='color: red;'>Error: Name, Address, and Class are required!</p>";
    } else {
        try {
            // 4. Insert Data: Prepare the SQL statement.
            // Using named parameters (:full_name, etc.) prevents SQL injection attacks.
            $sql = "INSERT INTO Pupils (full_name, address, medical_info, class_id) 
                    VALUES (:full_name, :address, :medical_info, :class_id)";
            
            $stmt = $pdo->prepare($sql);
            
            // Execute the query with the sanitized form data
            $stmt->execute([
                ':full_name' => $full_name,
                ':address' => $address,
                ':medical_info' => $medical_info,
                ':class_id' => $class_id
            ]);

            $message = "<p style='color: green;'>Success! New pupil added.</p>";
            
        } catch (PDOException $e) {
            // Catch any database errors (like connection issues) and show them to the user
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
    <link rel="stylesheet" href="../style.css">
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