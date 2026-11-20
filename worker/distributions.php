<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'worker'
) {
    header("Location: ../index.php");
    exit();
}

require_once "../dbconn.php";
require_once "../classes/application_manager.php";

$manager = new ApplicationManager($pdo);
$releases = $manager->fetch_all_releases();
$totalReleased = $manager->total_released_amount();
$escape = function ($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Distributions — Worker</title>
    <link rel="stylesheet" href="../theme.css">
</head>
<body>
<div class="dashboard">

    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="hamburger">☰</label>
    
    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="search.php">Search Residents</a>
        <a href="distributions.php" class="active">Distributions</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h2>Assistance Release History</h2>
                <p class="release-note">Completed releases are recorded from approved applications in Manage Applications.</p>
            </div>
        </div>

        <div class="stats-container release-summary">
            <section class="stat-card">
                <p>Total Releases</p>
                <h1><?php echo count($releases); ?></h1>
            </section>
            <section class="stat-card">
                <p>Total Assistance Released</p>
                <h1>₱<?php echo number_format((float)$totalReleased, 2); ?></h1>
            </section>
        </div>

        <section class="panel">
            <div class="panel-header">
                <h3>Release Records</h3>
            </div>

            <?php if (!$releases): ?>
                <p class="empty-message">No assistance releases have been recorded yet.</p>
                <p class="empty-message">
                    Once an application is approved, you can record its release from
                    <a class="release-link" href="manage_applications.php">Manage Applications</a>.
                </p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="applicant-table">
                        <thead>
                            <tr>
                                <th>Release ID</th>
                                <th>Resident</th>
                                <th>Application</th>
                                <th>Assistance Program</th>
                                <th>Amount</th>
                                <th>Release Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($releases as $release): ?>
                            <tr>
                                <td><?php echo $escape($release['release_id']); ?></td>
                                <td>
                                    <?php if (!empty($release['resident_id'])): ?>
                                        <a class="release-link" href="resident_management.php?edit=<?php echo urlencode((string)$release['resident_id']); ?>">
                                            <?php echo $escape($release['resident_name'] ?: 'Resident'); ?>
                                        </a>
                                        <small>Resident #<?php echo $escape($release['resident_id']); ?></small>
                                    <?php else: ?>
                                        <?php echo $escape($release['resident_name'] ?: 'Unlinked resident'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="release-link" href="manage_applications.php?edit=<?php echo urlencode((string)$release['applicant_id']); ?>">
                                        #<?php echo $escape($release['applicant_id']); ?>
                                    </a>
                                </td>
                                <td><?php echo $escape($release['aid_type']); ?></td>
                                <td class="amount-cell">₱<?php echo number_format((float)$release['amount'], 2); ?></td>
                                <td><?php echo $escape(date('M j, Y', strtotime($release['release_date']))); ?></td>
                                <td><?php echo $escape($release['remarks'] ?: '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
