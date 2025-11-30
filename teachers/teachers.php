<?php
/*
    TEACHERS DASHBOARD
    ------------------
    Displays teachers in a modern grid layout with avatars.
*/

include '../db.php';

// 1. Session Check
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['role'] ?? 'guest';

// 2. Fetch Teachers
$sql = "SELECT * FROM teachers ORDER BY full_name ASC";
$stmt = $pdo->query($sql);
$teachers = $stmt->fetchAll();

// Helper: Function to generate initials (e.g., "Emma Thompson" -> "ET")
function getInitials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) {
        $initials .= strtoupper($w[0]);
    }
    return substr($initials, 0, 2); // Max 2 letters
}

// Helper: Function to assign a random color to the avatar
function getAvatarColor($index) {
    $colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'];
    return $colors[$index % count($colors)];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teaching Staff</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Specific Styles for Teacher Cards */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .teacher-card {
            background-color: var(--bg-card);
            border: var(--border);
            border-radius: var(--radius);
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s;
        }

        .teacher-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }

        .teacher-info h3 {
            font-size: 1.1rem;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .teacher-info p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        .action-row {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Teaching Staff</h1>
            <a href="add_teacher.php" class="btn btn-primary">+ Add New Teacher</a>
        </div>

        <div class="grid-container">
            <?php foreach ($teachers as $index => $teacher): ?>
                <div class="teacher-card">
                    <!-- 1. The Avatar Circle -->
                    <div class="avatar" style="background-color: <?= getAvatarColor($index) ?>;">
                        <?= getInitials($teacher['full_name']) ?>
                    </div>

                    <!-- 2. Teacher Details -->
                    <div style="flex-grow: 1;">
                        <div class="teacher-info">
                            <h3><?= htmlspecialchars($teacher['full_name']) ?></h3>
                            <p>📞 <?= htmlspecialchars($teacher['phone']) ?></p>
                            <!-- Truncate address if it's too long -->
                            <p style="font-size: 0.8rem; margin-top: 4px;">
                                📍 <?= htmlspecialchars(substr($teacher['address'], 0, 25)) ?>...
                            </p>
                        </div>
                        
                        <!-- 3. Edit/Delete Buttons -->
                        <div class="action-row">
                            <a href="edit_teacher.php?id=<?= $teacher['teacher_id'] ?>" style="color: var(--warning); font-size: 0.85rem;">Edit</a>
                            <a href="delete_teacher.php?id=<?= $teacher['teacher_id'] ?>" 
                               style="color: var(--danger); font-size: 0.85rem;"
                               onclick="return confirm('Delete this teacher?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

                <?php include '../footer.php'; ?>

</body>
</html>