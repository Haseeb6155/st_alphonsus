<?php
/*
    LINK PARENT CONTROLLER
    ----------------------
    Handles assigning a parent to a pupil.
    Includes logic to count existing parents for better UX.
*/

include '../db.php';

$message = "";

// 1. DATA FETCHING: Get Pupils & Parent Counts
// We use a LEFT JOIN to count how many parents each student already has.
try {
    $pupils_sql = "SELECT Pupils.pupil_id, Pupils.full_name, COUNT(Pupil_Parent.parent_id) as parent_count 
                   FROM Pupils 
                   LEFT JOIN Pupil_Parent ON Pupils.pupil_id = Pupil_Parent.pupil_id 
                   GROUP BY Pupils.pupil_id 
                   ORDER BY Pupils.full_name ASC";
    $pupils = $pdo->query($pupils_sql)->fetchAll();

    // Get Parents List
    $parents_sql = "SELECT parent_id, full_name FROM Parents ORDER BY full_name ASC";
    $parents = $pdo->query($parents_sql)->fetchAll();
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// 2. FORM HANDLING: Process the Link Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pupil_id  = $_POST['pupil_id'] ?? '';
    $parent_id = $_POST['parent_id'] ?? '';

    if (empty($pupil_id) || empty($parent_id)) {
        $message = "<div class='status-pill status-inactive'>Please select both a Pupil and a Parent.</div>";
    } else {
        try {
            // A. Create the Link
            $insert_sql = "INSERT INTO Pupil_Parent (pupil_id, parent_id) VALUES (:pid, :paid)";
            $stmt = $pdo->prepare($insert_sql);
            $stmt->execute([':pid' => $pupil_id, ':paid' => $parent_id]);
            
            // B. Get Updated Count (for the success message)
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM Pupil_Parent WHERE pupil_id = ?");
            $count_stmt->execute([$pupil_id]);
            $total = $count_stmt->fetchColumn();

            $message = "<div class='status-pill status-active'>Link Created! (Student now has $total linked parents)</div>";
            
        } catch (PDOException $e) {
            // Error 23000 means "Duplicate Entry" (Parent is already linked to this specific student)
            if ($e->getCode() == 23000) {
                 $message = "<div class='status-pill status-warning'>This Parent is already linked to this Student!</div>";
            } else {
                 $message = "<div class='status-pill status-inactive'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Parent - St Alphonsus</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="centered-layout">

    <div class="form-card">
        <h2 class="mb-4">Link Parent & Pupil</h2>
        
        <?php if ($message): ?>
            <div style="margin-bottom: 20px; text-align: center;"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="form-group">
                <label>Select Pupil</label>
                <select name="pupil_id" required>
                    <option value="">-- Choose Pupil --</option>
                    <?php foreach ($pupils as $p): ?>
                        <option value="<?= $p['pupil_id'] ?>">
                            <?= htmlspecialchars($p['full_name']) ?>
                            <?= $p['parent_count'] > 0 ? ' (Has ' . $p['parent_count'] . ' parents)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Parent</label>
                <select name="parent_id" required>
                    <option value="">-- Choose Parent --</option>
                    <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['parent_id'] ?>">
                            <?= htmlspecialchars($p['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Link</button>
            
            <a href="parents.php" class="btn btn-sm" style="width: 100%; text-align: center; margin-top: 15px; background: transparent; color: var(--text-muted);">
                &larr; Back to Parents List
            </a>
        </form>
    </div>

</body>
</html>