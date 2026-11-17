
<?php
<<<<<<< HEAD
    require_once "../dbconn.php";
    require_once "../classes/applicant.php";

    $conn = new Applicant($pdo);
    $rows = $conn -> fetch_data();
    $totalApplicants = $conn -> count_applicants();
    $totalBenefits = $conn -> count_benefits();
    $recentApplicants = array_slice($rows, 0, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="worker_style.css">
    
=======
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

    <link rel="stylesheet" href="worker_style.css">

>>>>>>> 52fad0d (fixed profile and modified application form)
</head>

<body>

<div class="dashboard">

<<<<<<< HEAD
    
=======
    <!-- Sidebar -->
>>>>>>> 52fad0d (fixed profile and modified application form)
    <aside class="sidebar">

        <h3>Financial Aid System</h3>

<<<<<<< HEAD
        <a href="index.php" class="active">Dashboard</a>
    
        <a href="logout.php">Logout</a>

    </aside>
    <main class="main-content">
        <div class="topbar">

            <h2>Worker Dashboard</h2>
            <a href="search.php" class="search-clear">Search Residents</a>

<!--             <div class="user-label">
                Worker &#9662;
            </div> -->

        </div>
        <div class="stats-container">
            <div class="stat-card">
=======
        <a href="index.php" class="active">
            Dashboard
        </a>

        <a href="manage_applications.php">
            Manage Applications
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

>>>>>>> 52fad0d (fixed profile and modified application form)
                <p>Total Applicants</p>

                <h1>
                    <?php echo $totalApplicants['total']; ?>
                </h1>
<<<<<<< HEAD
            </div>
            <div class="stat-card" id="benefits">
=======

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

>>>>>>> 52fad0d (fixed profile and modified application form)
                <p>Available Benefits</p>

                <h1>
                    <?php echo $totalBenefits['total']; ?>
                </h1>
<<<<<<< HEAD
            </div>

        </div>
        <div class="content-grid">
            <section class="panel" id="applicants">

                <div class="panel-header">
                    <h3>Applicants</h3>
                </div>

=======

            </div>

        </div>


        <!-- Dashboard Content -->
        <div class="content-grid">


            <!-- Applicants Table -->
            <section class="panel" id="applicants">

                <div class="panel-header">

                    <h3>Applicants</h3>

                </div>


>>>>>>> 52fad0d (fixed profile and modified application form)
                <div class="table-wrapper">

                    <table class="applicant-table">

                        <thead>
<<<<<<< HEAD
                            <tr>
                                <th>Firstname</th>
                                <th>Middlename</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Benefit</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($rows as $row){ ?>

                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['firstname']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['middlename']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['lastname']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>

                                    <td>
                                        <span class="benefit-badge">
                                            <?php echo htmlspecialchars($row['aid_type']); ?>
                                        </span>
                                    </td>
=======

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

>>>>>>> 52fad0d (fixed profile and modified application form)
                                </tr>

                            <?php } ?>

<<<<<<< HEAD
                            <?php if(empty($rows)){ ?>

                                <tr>
                                    <td colspan="5" class="empty-message">
                                        No applicants found.
                                    </td>
=======

                            <?php if (empty($rows)) { ?>

                                <tr>

                                    <td colspan="5" class="empty-message">

                                        No applicants found.

                                    </td>

>>>>>>> 52fad0d (fixed profile and modified application form)
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </section>
<<<<<<< HEAD
=======


            <!-- Recent Applicants -->
>>>>>>> 52fad0d (fixed profile and modified application form)
            <section class="panel">

                <h3>Recent Applicants</h3>

<<<<<<< HEAD
                <div class="recent-list">

                    <?php if(!empty($recentApplicants)){ ?>

                        <?php foreach($recentApplicants as $recent){ ?>
=======

                <div class="recent-list">

                    <?php if (!empty($recentApplicants)) { ?>

                        <?php foreach ($recentApplicants as $recent) { ?>
>>>>>>> 52fad0d (fixed profile and modified application form)

                            <div class="recent-item">

                                <strong>
<<<<<<< HEAD
                                    <?php
                                        echo htmlspecialchars(
                                            $recent['firstname'] . " " .
                                            $recent['lastname']
                                        );
                                    ?>
                                </strong>

                                <small>
                                    <?php echo htmlspecialchars($recent['email']); ?>
                                </small>

                                <br>

                                <span class="benefit-badge">
                                    <?php echo htmlspecialchars($recent['aid_type']); ?>
=======

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

>>>>>>> 52fad0d (fixed profile and modified application form)
                                </span>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <p class="empty-message">
<<<<<<< HEAD
                            No recent applicants.
=======

                            No recent applicants.

>>>>>>> 52fad0d (fixed profile and modified application form)
                        </p>

                    <?php } ?>

                </div>

            </section>

<<<<<<< HEAD
        </div>
=======

        </div>

>>>>>>> 52fad0d (fixed profile and modified application form)
    </main>

</div>

</body>
</html>
