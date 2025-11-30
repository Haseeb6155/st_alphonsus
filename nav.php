<?php
// PHP LOGIC: Check who is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user tries to access this file directly without logging in
if (!isset($_SESSION['user_id'])) {
    // Adjust path dynamically or hardcode if your folder structure is fixed
    header("Location: /st_alphonsus/login.php");
    exit;
}

$role = $_SESSION['role'] ?? 'guest';
$current_page = basename($_SERVER['PHP_SELF']); // Get current file name for active state
?>

<!-- START NAV BAR HTML -->
<nav>
    <div class="nav-brand">
        St Alphonsus
        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-left: 10px;">
            <?= ucfirst($role) ?> Portal
        </span>
    </div>
    
    <div class="nav-links">
        <!-- 
           Dynamic Links: 
           We use simple PHP 'if' statements to decide which class='active' to add.
           This makes the underline effect work.
        -->

        <!-- Library is available to everyone -->
        <a href="/st_alphonsus/libraryy/library.php" 
           class="<?= $current_page == 'library.php' ? 'active' : '' ?>">
           Library
        </a>

        <?php if ($role == 'teacher' || $role == 'admin' || $role == 'parent'): ?>
            <a href="/st_alphonsus/Pupils/index.php" 
               class="<?= ($current_page == 'index.php' || $current_page == 'add_pupil.php') ? 'active' : '' ?>">
               Pupils
            </a>
            
            <a href="/st_alphonsus/attendance/attendance.php" 
               class="<?= $current_page == 'attendance.php' ? 'active' : '' ?>">
               Attendance
            </a>
        <?php endif; ?>

        <?php if ($role == 'admin'): ?>
            <a href="/st_alphonsus/teachers/teachers.php" 
               class="<?= $current_page == 'teachers.php' ? 'active' : '' ?>">
               Teachers
            </a>
            
            <a href="/st_alphonsus/classess/classes.php" 
               class="<?= $current_page == 'classes.php' ? 'active' : '' ?>">
               Classes
            </a>
            
            <a href="/st_alphonsus/parents/parents.php" 
               class="<?= $current_page == 'parents.php' ? 'active' : '' ?>">
               Parents
            </a>
        <?php endif; ?>

        <!-- Logout Button -->
        <a href="/st_alphonsus/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

