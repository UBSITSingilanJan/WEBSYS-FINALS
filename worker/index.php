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
require_once "../classes/applicant.php";
require_once "../classes/application_manager.php";
require_once "../classes/resident.php";

$conn = new Applicant($pdo);
$applicationManager = new ApplicationManager($pdo);
$residentConn = new Resident($pdo);
$rows = $conn->fetch_data();

$totalApplicants = $conn->count_applicants();
$totalBenefits = $conn->count_benefits();
$totalResidents = $residentConn->count_residents();
$totalReleased = $applicationManager->total_released_amount();
$recentApplicants = array_slice($rows, 0, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="../theme.css">
</head>
<body>
    <div class="dashboard">
        <?php
        $activePage = 'dashboard';
        require __DIR__ . '/sidebar.php';
        ?>

        <main class="main-content">
            <div class="topbar">
                <h2>Worker Dashboard</h2>
                <a href="resident_management.php" class="search-clear">
                    Manage Residents
                </a>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <p>Total Applicants</p>
                    <h1><?= htmlspecialchars((string)$totalApplicants['total']) ?></h1>
                </div>

                <div class="stat-card">
                    <p>Total Residents</p>
                    <h1><?= htmlspecialchars((string)$totalResidents['total']) ?></h1>
                </div>

                <div class="stat-card" id="benefits">
                    <p>Available Benefits</p>
                    <h1><?= htmlspecialchars((string)$totalBenefits['total']) ?></h1>
                </div>

                <div class="stat-card">
                    <p>Total Released Amount</p>
                    <h1>₱<?= number_format((float)$totalReleased, 2) ?></h1>
                </div>
            </div>

            <div class="content-grid">
                <section class="panel" id="applicants">
                    <div class="panel-header">
                        <h3>Applicants</h3>
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
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['firstname'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['middlename'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['lastname'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                        <td>
                                            <span class="benefit-badge">
                                                <?= htmlspecialchars($row['aid_type'] ?? '') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="5" class="empty-message">
                                            No applicants found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel">
                    <h3>Recent Applicants</h3>

                    <div class="recent-list">
                        <?php if (!empty($recentApplicants)): ?>
                            <?php foreach ($recentApplicants as $recent): ?>
                                <div class="recent-item">
                                    <strong>
                                        <?= htmlspecialchars(
                                            ($recent['firstname'] ?? '') . ' ' .
                                            ($recent['lastname'] ?? '')
                                        ) ?>
                                    </strong>
                                    <small>
                                        <?= htmlspecialchars($recent['email'] ?? '') ?>
                                    </small>
                                    <br>
                                    <span class="benefit-badge">
                                        <?= htmlspecialchars($recent['aid_type'] ?? '') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">
                                No recent applicants.
                            </p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
