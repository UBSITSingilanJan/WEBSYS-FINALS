
<?php
<<<<<<< HEAD
    session_start();

    require_once "../dbconn.php";
    require_once "../classes/applicant.php";
    if(!isset($_SESSION['user_id'])){
        header("Location: ../index.php");
        exit();
    }

    $conn = new Applicant($pdo);
    $applications = $conn -> fetch_resident_applications($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
=======
session_start();

require_once "../dbconn.php";
require_once "../classes/applicant.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$conn = new Applicant($pdo);

$applications = $conn->fetch_resident_applications($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

>>>>>>> 52fad0d (fixed profile and modified application form)
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Resident Dashboard</title>

    <link rel="stylesheet" href="resident_style.css">
<<<<<<< HEAD

    <style>
        .application-item {
            padding: 15px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .application-item:last-child {
            border-bottom: none;
        }

        .application-item h4 {
            margin-bottom: 8px;
            font-size: 15px;
        }

        .application-item p {
            color: #777;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .approved {
            background: #d1e7dd;
            color: #0f5132;
        }

        .rejected {
            background: #f8d7da;
            color: #842029;
        }

        .unknown {
            background: #e2e3e5;
            color: #41464b;
        }
    </style>
=======
>>>>>>> 52fad0d (fixed profile and modified application form)
</head>

<body>

<div class="dashboard">

<<<<<<< HEAD
    <!-- Sidebar -->
=======
>>>>>>> 52fad0d (fixed profile and modified application form)
    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="index.php" class="active">My Dashboard</a>
        <a href="profile.php">My Profile</a>
        <a href="../application/index.php">Apply Now</a>
        <a href="logout.php">Logout</a>

    </aside>
<<<<<<< HEAD
    <main class="main-content">
        <div class="topbar">

            <h2>My Aid Application</h2>

            <div class="user-label">
                Applicant &#9662;
            </div>

        </div>
        <div class="content-grid">
=======

    <main class="main-content">

        <div class="topbar">
            <h2>My Aid Application</h2>
        </div>

        <div class="content-grid">

>>>>>>> 52fad0d (fixed profile and modified application form)
            <section class="panel">

                <h3>My Applications</h3>

<<<<<<< HEAD
                <?php if(!empty($applications)){ ?>

                    <?php foreach($applications as $application){ ?>

                        <?php
                            $status = strtolower($application['status']);

                            if($status == "pending"){
                                $statusClass = "pending";
                            } elseif($status == "approved"){
                                $statusClass = "approved";
                            } elseif($status == "rejected"){
                                $statusClass = "rejected";
                            } else {
                                $statusClass = "unknown";
                            }
=======
                <?php if (!empty($applications)) { ?>

                    <?php foreach ($applications as $application) { ?>

                        <?php
                        $status = strtolower($application['status']);

                        if ($status == "pending") {
                            $statusClass = "pending";
                        } elseif ($status == "approved") {
                            $statusClass = "approved";
                        } elseif ($status == "rejected") {
                            $statusClass = "rejected";
                        } else {
                            $statusClass = "unknown";
                        }
>>>>>>> 52fad0d (fixed profile and modified application form)
                        ?>

                        <div class="application-item">

                            <h4>
<<<<<<< HEAD
                                <?php echo htmlspecialchars($application['aid_type']); ?>
=======
                                <?php
                                echo htmlspecialchars($application['aid_type']);
                                ?>
>>>>>>> 52fad0d (fixed profile and modified application form)
                            </h4>

                            <p>
                                Applicant:
                                <?php
<<<<<<< HEAD
                                    echo htmlspecialchars(
                                        $application['firstname'] . " " .
                                        $application['lastname']
                                    );
=======
                                echo htmlspecialchars(
                                    $application['firstname'] . " " .
                                    $application['lastname']
                                );
>>>>>>> 52fad0d (fixed profile and modified application form)
                                ?>
                            </p>

                            <p>
                                Email:
<<<<<<< HEAD
                                <?php echo htmlspecialchars($application['email']); ?>
                            </p>

                            <p>
                                Application Status:
                            </p>

                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($application['status']); ?>
=======
                                <?php
                                echo htmlspecialchars($application['email']);
                                ?>
                            </p>

                            <p>Application Status:</p>

                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php
                                echo htmlspecialchars($application['status']);
                                ?>
>>>>>>> 52fad0d (fixed profile and modified application form)
                            </span>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="empty-message">

                        You haven't submitted any applications yet.

                        <br><br>

                        <a href="../application/index.php" class="btn-primary">
                            Apply Now
                        </a>

                    </div>

                <?php } ?>

            </section>
<<<<<<< HEAD
            <section class="panel">
                <h3>Application Status</h3>

                <?php if(!empty($applications)){ ?>

                    <?php
                        $latestApplication = $applications[0];
                        $latestStatus = $latestApplication['status'];
                    ?>

                    <div class="status-number">
                        <?php echo htmlspecialchars($latestStatus); ?>
                    </div>

                    <p class="status-description">
                        Your latest application for
                        <strong>
                            <?php echo htmlspecialchars($latestApplication['aid_type']); ?>
                        </strong>
                        is currently
                        <strong>
                            <?php echo htmlspecialchars($latestStatus); ?>
                        </strong>.
                    </p>

                    <br>

                    <p class="status-description">
                        Updates from the assigned worker will appear here.
                    </p>

                <?php } else { ?>

                    <div class="status-number">&mdash;</div>

                    <p class="status-description">
                        You don't have an application yet.
                        Submit an application to see your status here.
                    </p>

                    <br>

                <?php } ?>
            </section>
        </div>
=======

        </div>

>>>>>>> 52fad0d (fixed profile and modified application form)
    </main>

</div>

</body>
</html>
