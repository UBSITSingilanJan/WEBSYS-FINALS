<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'resident') {
    header("Location: ../index.php");
    exit();
}
if (!empty($_SESSION['must_change_password'])) {
    header("Location: change_password.php");
    exit();
}
require_once "../dbconn.php";
require_once "../classes/program_manager.php";
$manager = new ProgramManager($pdo);
$programs = $manager->fetch_active_programs();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Programs</title>
    <link rel="stylesheet" href="../theme.css">
    <style>
        .program-card { background:#fff; border:1px solid #dce2e8; border-radius:10px; padding:22px; margin-bottom:18px; }
        .program-card h3 { margin:0 0 12px; color:#123f68; }
        .program-card ul { margin:10px 0 0; padding-left:22px; }
        .program-card li { margin:6px 0; }
        .program-card .program-apply { display:inline-block; margin-top:14px; }
    </style>
</head>
<body>
<div class="dashboard">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="hamburger">☰</label>
    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">My Dashboard</a>
        <a href="profile.php">My Profile</a>
        <a href="programs.php" class="active">Available Programs</a>
        <a href="../application/index.php">Apply Now</a>
        <a href="logout.php">Logout</a>
    </aside>
    <main class="main-content">
        <div class="topbar"><h2>Available Assistance Programs</h2></div>
        <?php if (!$programs): ?>
            <section class="panel"><p>No active assistance programs are available right now.</p></section>
        <?php else: ?>
            <?php foreach ($programs as $program): ?>
                <?php $requirements = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$program['description'])))); ?>
                <section class="program-card">
                    <h3><?= e($program['program_name']) ?></h3>
                    <strong>Requirements</strong>
                    <?php if ($requirements): ?>
                        <ul><?php foreach ($requirements as $requirement): ?><li><?= e($requirement) ?></li><?php endforeach; ?></ul>
                    <?php else: ?>
                        <p>Requirements have not been listed yet. Please contact the barangay office.</p>
                    <?php endif; ?>
                    <a class="btn-primary program-apply" href="../application/index.php">Apply Now</a>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
