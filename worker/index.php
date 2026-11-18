
<?php

session_start();

// Check if the logged-in user is a worker
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'worker'
) {
    header("Location: ../index.php");
    exit();
}

require_once "../dbconn.php";
require_once "../classes/applicant.php";
require_once "../classes/resident.php";
require_once "../classes/program_manager.php";

// Initialize classes
$conn = new Applicant($pdo);
$residentConn = new Resident($pdo);
$programConn = new ProgramManager($pdo);

$rows = $conn->fetch_data();

$totalApplicants = $conn->count_applicants();
$totalResidents = $residentConn->count_residents();

$programCounts = $programConn->count_programs($_SESSION['user_id']);

$totalPrograms = $programCounts['total'] ?? 0;
$activePrograms = $programCounts['active'] ?? 0;
$inactivePrograms = $programCounts['inactive'] ?? 0;
$recentApplicants = array_slice($rows, 0, 4);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Worker Dashboard</title>

    <link rel="stylesheet" href="worker_style.css">
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <nav>
            <a href="index.php" class="active">Dashboard</a>
            <a href="manage_programs.php">Assistance Programs</a>
            <a href="manage_applications.php">Manage Applications</a>
            <a href="logout.php">Logout</a>
        </nav>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <!-- Topbar -->
        <header class="topbar">

            <div>
                <h1>Worker Dashboard</h1>
                <p>Welcome to your financial aid management dashboard.</p>
            </div>

            <a href="search.php" class="search-button">
                Search Residents
            </a>

        </header>


        <!-- Dashboard Statistics -->
        <section class="stats-container">

            <!-- Total Applicants -->
            <div class="stat-card">

                <div class="stat-icon applicants-icon">
                    👥
                </div>

                <div class="stat-info">
                    <p>Total Applicants</p>
                    <h2>
                        <?= htmlspecialchars($totalApplicants['total'] ?? 0); ?>
                    </h2>
                    <span>All submitted applications</span>
                </div>

            </div>


            <!-- Total Residents -->
            <div class="stat-card">

                <div class="stat-icon residents-icon">
                    🏠
                </div>

                <div class="stat-info">
                    <p>Total Residents</p>
                    <h2>
                        <?= htmlspecialchars($totalResidents['total'] ?? 0); ?>
                    </h2>
                    <span>Registered residents</span>
                </div>

            </div>


            <!-- Total Programs -->
            <div class="stat-card">

                <div class="stat-icon programs-icon">
                    📋
                </div>

                <div class="stat-info">
                    <p>Total Programs</p>
                    <h2>
                        <?= htmlspecialchars($totalPrograms); ?>
                    </h2>
                    <span>Programs you have added</span>
                </div>

            </div>


            <!-- Active Programs -->
            <div class="stat-card">

                <div class="stat-icon active-icon">
                    ✓
                </div>

                <div class="stat-info">
                    <p>Active Programs</p>
                    <h2>
                        <?= htmlspecialchars($activePrograms); ?>
                    </h2>
                    <span>Currently available programs</span>
                </div>

            </div>


            <!-- Deactivated Programs -->
            <div class="stat-card">

                <div class="stat-icon inactive-icon">
                    ✕
                </div>

                <div class="stat-info">
                    <p>Deactivated Programs</p>
                    <h2>
                        <?= htmlspecialchars($inactivePrograms); ?>
                    </h2>
                    <span>Currently inactive programs</span>
                </div>

            </div>

        </section>


        <!-- Applicants Section -->
        <section class="content-grid">

            <div class="panel">

                <div class="panel-header">
                    <h2>Applicants</h2>
                    <a href="manage_applications.php">View All</a>
                </div>

                <div class="table-wrapper">

                    <table class="applicant-table">

                        <thead>
                            <tr>
                                <th>Firstname</th>
                                <th>Middlename</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Benefit</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($rows)): ?>

                                <?php foreach ($rows as $row): ?>

                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($row['firstname'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['middlename'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['lastname'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['email'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <span class="benefit-badge">
                                                <?= htmlspecialchars($row['aid_type'] ?? ''); ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5" class="empty-message">
                                        No applicants found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- Recent Applicants -->
            <div class="panel recent-panel">

                <div class="panel-header">
                    <h2>Recent Applicants</h2>
                </div>

                <div class="recent-list">

                    <?php if (!empty($recentApplicants)): ?>

                        <?php foreach ($recentApplicants as $applicant): ?>

                            <div class="recent-item">

                                <div class="recent-avatar">
                                    <?= strtoupper(substr($applicant['firstname'] ?? 'A', 0, 1)); ?>
                                </div>

                                <div class="recent-info">

                                    <h3>
                                        <?= htmlspecialchars(
                                            ($applicant['firstname'] ?? '') . ' ' .
                                            ($applicant['lastname'] ?? '')
                                        ); ?>
                                    </h3>

                                    <p>
                                        <?= htmlspecialchars($applicant['email'] ?? ''); ?>
                                    </p>

                                    <span>
                                        <?= htmlspecialchars($applicant['aid_type'] ?? ''); ?>
                                    </span>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="empty-message">
                            No recent applicants.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
