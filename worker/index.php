
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

    <?php $activePage = 'dashboard'; require __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">

            <h2>Worker Dashboard</h2>

            <a href="resident_management.php" class="search-clear">
                Manage Residents
            </a>

        </div>


        <!-- Dashboard Statistics -->
        <div class="stats-container">

            <!-- Total Applicants -->
            <div class="stat-card">

                <p>Total Applicants</p>

                <h1>
                    <?php echo $totalApplicants['total']; ?>
                </h1>

            </div>


            <!-- Total Residents -->
            <div class="stat-card">

                <p>Total Residents</p>

                <h1>
                    <?php echo $totalResidents['total']; ?>
                </h1>

            </div>


            <!-- Available Benefits -->
            <div class="stat-card" id="benefits">

                <p>Available Benefits</p>

                <h1>
                    <?php echo $totalBenefits['total']; ?>
                </h1>

            </div>


            <!-- Total Released Amount -->
            <div class="stat-card">

                <p>Total Released Amount</p>

                <h1>
                    ₱<?php echo number_format((float)$totalReleased, 2); ?>
                </h1>

            </div>

        </div>


        <!-- Dashboard Content -->
        <div class="content-grid">


            <!-- Applicants Table -->
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

                            <?php foreach ($rows as $row) { ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['firstname'] ?? ''
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['middlename'] ?? ''
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['lastname'] ?? ''
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['email'] ?? ''
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <span class="benefit-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['aid_type'] ?? ''
                                            );
                                            ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php } ?>


                            <?php if (empty($rows)) { ?>

                                <tr>

                                    <td colspan="5" class="empty-message">

                                        No applicants found.

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- Recent Applicants -->
            <section class="panel">

                <h3>Recent Applicants</h3>


                <div class="recent-list">

                    <?php if (!empty($recentApplicants)) { ?>

                        <?php foreach ($recentApplicants as $recent) { ?>

                            <div class="recent-item">

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        ($recent['firstname'] ?? '') . " " .
                                        ($recent['lastname'] ?? '')
                                    );

                                    ?>

                                </strong>


                                <small>

                                    <?php

                                    echo htmlspecialchars(
                                        $recent['email'] ?? ''
                                    );

                                    ?>

                                </small>


                                <br>


                                <span class="benefit-badge">

                                    <?php

                                    echo htmlspecialchars(
                                        $recent['aid_type'] ?? ''
                                    );

                                    ?>

                                </span>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <p class="empty-message">

                            No recent applicants.

                        </p>

                    <?php } ?>

                </div>

            </section>


        </div>

    </main>

</div>

</body>
</html>
