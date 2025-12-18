<?php
// Navigation component: Handles session checks and role-based menu rendering

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Initialize variables with defaults to prevent 'undefined variable' errors
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);
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
            <a href="/Pupils/index.php" 
               class="<?= ($current_page == 'index.php' || $current_page == 'add_pupil.php') ? 'active' : '' ?>">
               Pupils
            </a>
            
            <a href="/attendance/attendance.php" 
               class="<?= $current_page == 'attendance.php' ? 'active' : '' ?>">
               Attendance
            </a>
        <?php endif; ?>

        <?php // Administrative links restricted to Admins only ?>
        <?php if ($role == 'admin'): ?>
            <a href="/teachers/teachers.php" 
               class="<?= $current_page == 'teachers.php' ? 'active' : '' ?>">
               Teachers
            </a>
            
            <a href="/classess/classes.php" 
               class="<?= $current_page == 'classes.php' ? 'active' : '' ?>">
               Classes
            </a>
        <?php endif; ?>

        <?php // Parent management restricted to Staff (Admins and Teachers) ?>
        <?php if ($role == 'admin' || $role == 'teacher'): ?>
            <a href="/parents/parents.php" 
               class="<?= $current_page == 'parents.php' ? 'active' : '' ?>">
               Parents
            </a>
        <?php endif; ?>

        <a href="/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>