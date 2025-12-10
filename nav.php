<nav>
    <div class="nav-brand">
        St Alphonsus
        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-left: 10px;">
            <?= ucfirst($role) ?> Portal 
            <span style="color: var(--primary); margin-left: 5px;">
               (<?= htmlspecialchars($_SESSION['username'] ?? '') ?>)
            </span>
        </span>
    </div>
    
    <div class="nav-links">
        
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
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'teacher'): ?>
            <a href="/st_alphonsus/parents/parents.php" 
               class="<?= $current_page == 'parents.php' ? 'active' : '' ?>">
               Parents
            </a>
        <?php endif; ?>

        <a href="/st_alphonsus/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>