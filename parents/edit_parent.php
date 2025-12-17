<?php
// Edit parent details and manage student associations
include '../db.php';

session_start();

// Restrict access to administrators and teachers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'parent') {
    header("Location: ../index.php");
    exit;
}

// Ensure a valid parent ID is provided
if (!isset($_GET['id'])) {
    header("Location: parents.php");
    exit;
}

$id = $_GET['id'];
$message = "";

// Handle request to unlink a student from this parent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unlink_pupil_id'])) {
    try {
        // Remove the specific link record from the database
        $unlink_sql = "DELETE FROM Pupil_Parent WHERE parent_id = :paid AND pupil_id = :pid";
        $stmt = $pdo->prepare($unlink_sql);
        $stmt->execute([
            ':paid' => $id,
            ':pid'  => $_POST['unlink_pupil_id']
        ]);
        $message = "<div class='status-pill status-active'>Link removed successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='status-pill status-inactive'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Process form submission to update parent details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_parent'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);

    // Validate required fields
    if (empty($full_name) || empty($phone)) {
        $message = "<div class='status-pill status-inactive'>Full Name and Phone are required.</div>";
    } else {
        try {
            // Update parent record securely
            $update_sql = "UPDATE Parents 
                           SET full_name = :name, email = :email, phone = :phone, address = :addr 
                           WHERE parent_id = :id";
            
            $stmt = $pdo->prepare($update_sql);
            $stmt->execute([
                ':name'  => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':addr'  => $address,
                ':id'    => $id
            ]);
            
            $message = "<div class='status-pill status-active'>Parent details updated successfully!</div>";
        } catch (PDOException $e) {
            $message = "<div class='status-pill status-inactive'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// Retrieve current parent data for the form
try {
    $stmt = $pdo->prepare("SELECT * FROM Parents WHERE parent_id = :id");
    $stmt->execute([':id' => $id]);
    $parent = $stmt->fetch();

    if (!$parent) { die("Error: Parent not found."); }

    // Fetch list of students currently linked to this parent
    $kids_sql = "SELECT Pupils.pupil_id, Pupils.full_name 
                 FROM Pupils 
                 JOIN Pupil_Parent ON Pupils.pupil_id = Pupil_Parent.pupil_id 
                 WHERE Pupil_Parent.parent_id = :id";
    $kids_stmt = $pdo->prepare($kids_sql);
    $kids_stmt->execute([':id' => $id]);
    $linked_kids = $kids_stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Parent - St Alphonsus</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Specific styles for the linked students list */
        .link-manager { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; }
        .link-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.03);
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .link-row span { font-weight: 500; font-size: 0.95rem; }
        .unlink-btn {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: 0.2s;
        }
        .unlink-btn:hover { background: var(--danger); color: white; }
    </style>
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Edit Parent Record</h2>
        
        <?php if ($message): ?>
            <div style="margin-bottom: 20px;"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="update_parent" value="true">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($parent['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($parent['email']) ?>">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($parent['phone']) ?>" required>
            </div>

            <div class="form-group">
                <label>Home Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($parent['address']) ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
        </form>

        <div class="link-manager">
            <h3 style="font-size: 1.1rem; margin-bottom: 15px; color: var(--text-main);">Linked Children</h3>

            <?php if (count($linked_kids) > 0): ?>
                <?php foreach ($linked_kids as $kid): ?>
                    <div class="link-row">
                        <span><?= htmlspecialchars($kid['full_name']) ?></span>
                        
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this link?');">
                            <input type="hidden" name="unlink_pupil_id" value="<?= $kid['pupil_id'] ?>">
                            <button type="submit" class="unlink-btn">Unlink</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">
                    No children currently linked to this parent.
                </p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <a href="parents.php" class="btn btn-sm" style="width: 100%; text-align: center; background: transparent; color: var(--text-muted);">
                &larr; Back to Parents List
            </a>
        </div>
    </div>

</body>
</html>