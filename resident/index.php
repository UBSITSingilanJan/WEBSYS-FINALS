
<?php
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

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Resident Dashboard</title>

    <link rel="stylesheet" href="../theme.css">
</head>

<body>

<div class="dashboard">

    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="hamburger">☰</label>

    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="index.php" class="active">My Dashboard</a>
        <a href="profile.php">My Profile</a>
        <a href="../application/index.php">Apply Now</a>
        <a href="logout.php">Logout</a>

    </aside>

    <main class="main-content">

        <div class="topbar">
            <h2>My Aid Application</h2>
        </div>

        <div class="content-grid">

            <section class="panel">

                <h3>My Applications</h3>

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
                        ?>

                        <div class="application-item">

                            <h4>
                                <?php
                                echo htmlspecialchars($application['aid_type']);
                                ?>
                            </h4>

                            <p>
                                Applicant:
                                <?php
                                echo htmlspecialchars(
                                    $application['firstname'] . " " .
                                    $application['lastname']
                                );
                                ?>
                            </p>

                            <p>
                                Email:
                                <?php
                                echo htmlspecialchars($application['email']);
                                ?>
                            </p>

                            <p>Application Status:</p>

                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php
                                echo htmlspecialchars($application['status']);
                                ?>
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

        </div>

    </main>

</div>

</body>
</html>
