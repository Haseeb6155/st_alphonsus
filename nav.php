<?php
// Navigation component: Handles session checks and role-based menu rendering

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Initialize variables with defaults to prevent 'undefined variable' errors
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);

// FIXED: Define a Base URL. 
// This ensures links work correctly even inside subfolders (e.g. localhost/st_alphonsus/)
$base_url = '/st_alphonsus'; 
?>

<nav>
    <div class="nav-brand">
        St Alphonsus
        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-left: 10px;">
            <?= ucfirst($role) ?> Portal 
            <span style="color: var(--primary); margin-left: 5px;">
                (<?= htmlspecialchars($username) ?>)
            </span>
        </span>
    </div>
    
    <div class="nav-links">
        
        <?php // General access links for Staff and Parents ?>
        <?php if ($role == 'teacher' || $role == 'admin' || $role == 'parent'): ?>
            <a href="<?= $base_url ?>/pupils/index.php" 
               class="<?= ($current_page == 'index.php' || $current_page == 'add_pupil.php') ? 'active' : '' ?>">
               Pupils
            </a>
            
            <a href="<?= $base_url ?>/attendance/attendance.php" 
               class="<?= $current_page == 'attendance.php' ? 'active' : '' ?>">
               Attendance
            </a>
        <?php endif; ?>

        <?php // Administrative links restricted to Admins only ?>
        <?php if ($role == 'admin'): ?>
            <a href="<?= $base_url ?>/teachers/teachers.php" 
               class="<?= $current_page == 'teachers.php' ? 'active' : '' ?>">
               Teachers
            </a>
            
            <a href="<?= $base_url ?>/classess/classes.php" 
               class="<?= $current_page == 'classess.php' ? 'active' : '' ?>">
               Classes
            </a>
        <?php endif; ?>

        <?php // Parent management restricted to Staff (Admins and Teachers) ?>
        <?php if ($role == 'admin' || $role == 'teacher'): ?>
            <a href="<?= $base_url ?>/parents/parents.php" 
               class="<?= $current_page == 'parents.php' ? 'active' : '' ?>">
               Parents
            </a>
        <?php endif; ?>

        <a href="<?= $base_url ?>/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>