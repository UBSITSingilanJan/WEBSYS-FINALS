
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
    require_once "../classes/resident.php";

    $conn = new Applicant($pdo);
    $residentConn = new Resident($pdo);
    $rows = $conn->fetch_data();

    $totalApplicants = $conn->count_applicants();
    $totalBenefits = $conn->count_benefits();
    $totalResidents = $residentConn->count_residents();
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

   <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="hamburger">☰</label>
    
    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="index.php" class="active">
            Dashboard
        </a>

        <a href="manage_applications.php">
            Manage Applications
        </a>

        <a href="manage_programs.php">
            Manage Programs
        </a>

        <a href="logout.php">
            Logout
        </a>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">

            <h2>Worker Dashboard</h2>

            <a href="search.php" class="search-clear">
                Search Residents
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
