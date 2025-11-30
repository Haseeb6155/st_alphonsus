<?php
/*
    CLASS DASHBOARD
    ---------------
    Displays classes as colorful cards like your screenshot.
*/
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Fetch Classes AND count how many pupils are in each class
$sql = "SELECT Classes.*, Teachers.full_name, 
        (SELECT COUNT(*) FROM Pupils WHERE Pupils.class_id = Classes.class_id) as student_count
        FROM Classes 
        LEFT JOIN Teachers ON Classes.teacher_id = Teachers.teacher_id";

$stmt = $pdo->query($sql);
$classes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Management</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* CSS Grid for the Class Cards */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        /* The Colorful Card */
        .class-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            position: relative;
        }

        .class-card:hover { transform: translateY(-5px); }

        /* The Colored Header (Gradient) */
        .card-header {
            padding: 20px;
            color: white;
        }

        /* Helper classes for different gradients */
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .gradient-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

        .class-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; }
        .teacher-name { font-size: 0.9rem; opacity: 0.9; }

        .card-body { padding: 20px; }
        
        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 15px;
        }

        .stat-item { text-align: center; }
        .stat-number { font-size: 1.5rem; font-weight: bold; color: var(--text-main); }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Class Management</h1>
            <a href="add_class.php" class="btn btn-primary">+ Create New Class</a>
        </div>

        <div class="classes-grid">
            <?php 
                // Array of gradient classes to cycle through
                $gradients = ['gradient-blue', 'gradient-purple', 'gradient-pink', 'gradient-orange'];
                $i = 0;
            ?>
            
            <?php foreach ($classes as $class): ?>
                <div class="class-card">
                    <!-- 1. Colorful Header -->
                    <div class="card-header <?= $gradients[$i % 4] ?>">
                        <div class="class-title"><?= htmlspecialchars($class['class_name']) ?></div>
                        <div class="teacher-name"><?= htmlspecialchars($class['full_name'] ?? 'No Teacher Assigned') ?></div>
                    </div>

                    <!-- 2. Statistics Body -->
                    <div class="card-body">
                        <div class="stat-row">
                            <div class="stat-item">
                                <div class="stat-number"><?= $class['student_count'] ?></div>
                                <div class="stat-label">Students</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?= htmlspecialchars($class['capacity']) ?></div>
                                <div class="stat-label">Capacity</div>
                            </div>
                        </div>

                        <div class="card-actions">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">
                                ID: #<?= $class['class_id'] ?>
                            </span>
                            <div>
                                <a href="edit_class.php?id=<?= $class['class_id'] ?>" class="btn btn-sm" style="background: #374151;">Edit</a>
                                <a href="delete_class.php?id=<?= $class['class_id'] ?>" 
                                   class="btn btn-sm" 
                                   style="background: transparent; color: var(--danger); border: 1px solid var(--danger);"
                                   onclick="return confirm('Delete this class?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; // Increment to change color for next card ?>
            <?php endforeach; ?>
        </div>
    </div>
             <?php include '../footer.php'; ?>   
</body>
</html>