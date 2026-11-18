
<?php
session_start();

require_once "../dbconn.php";
require_once "../classes/applicant.php";
require_once "../classes/program_manager.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$applicant = new Applicant($pdo);
$programManager = new ProgramManager($pdo);

$programs = $programManager->fetch_active_programs();

$error = "";

if (isset($_POST['apply_btn'])) {

    $firstname = trim($_POST['firstname'] ?? "");
    $middlename = trim($_POST['middlename'] ?? "");
    $lastname = trim($_POST['lastname'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $program_id = (int)($_POST['program_id'] ?? 0);

    // Confirm that the selected program exists and is active.
    $selectedProgram = null;

    foreach ($programs as $program) {
        if ((int)$program['program_id'] === $program_id) {
            $selectedProgram = $program;
            break;
        }
    }

    if (
        $firstname === "" ||
        $middlename === "" ||
        $lastname === "" ||
        $email === "" ||
        !$selectedProgram
    ) {
        $error = "Please complete all fields and select a valid assistance program.";
    } else {

        $aid_type = $selectedProgram['program_name'];
        $user_id = $_SESSION['user_id'];

        try {
            $applicant->insert_data(
                $firstname,
                $middlename,
                $lastname,
                $email,
                $aid_type,
                $user_id
            );

            header("Location: ../resident/index.php");
            exit();

        } catch (PDOException $e) {
            $error = "Unable to submit your application. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apply for Assistance</title>

    <link rel="stylesheet" href="../resident/resident_style.css">
    <link rel="stylesheet" href="application_style.css">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="../resident/index.php">My Dashboard</a>
        <a href="../resident/profile.php">My Profile</a>
        <a href="index.php" class="active">Apply Now</a>
        <a href="../resident/logout.php">Logout</a>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="topbar">
            <div>
                <h2>Financial Aid Application</h2>
                <p class="page-subtitle">
                    Apply for an available assistance program.
                </p>
            </div>
        </div>

        <div class="content-grid">

            <section class="panel application-panel">

                <div class="application-heading">
                    <h3>Apply for an Assistance Program</h3>
                    <p>
                        Complete the form below to apply for an available
                        assistance program.
                    </p>
                </div>

                <?php if ($error !== ""): ?>
                    <div class="application-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="form-group">
                        <label for="firstname">Firstname</label>
                        <input
                            type="text"
                            id="firstname"
                            name="firstname"
                            class="form-control"
                            placeholder="Enter your firstname"
                            value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="middlename">Middlename</label>
                        <input
                            type="text"
                            id="middlename"
                            name="middlename"
                            class="form-control"
                            placeholder="Enter your middlename"
                            value="<?= htmlspecialchars($_POST['middlename'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Lastname</label>
                        <input
                            type="text"
                            id="lastname"
                            name="lastname"
                            class="form-control"
                            placeholder="Enter your lastname"
                            value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter your email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="program_id">Assistance Program</label>

                        <select
                            name="program_id"
                            id="program_id"
                            class="form-control"
                            required>

                            <option value="">Select an assistance program</option>

                            <?php foreach ($programs as $program): ?>
                                <option
                                    value="<?= (int)$program['program_id'] ?>"
                                    <?= (isset($_POST['program_id']) &&
                                        (int)$_POST['program_id'] === (int)$program['program_id'])
                                        ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($program['program_name']) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>

                        <?php if (empty($programs)): ?>
                            <p class="no-program-message">
                                There are currently no active assistance programs available.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions application-actions">

                        <a href="../resident/index.php" class="btn-secondary">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            name="apply_btn"
                            class="btn-primary"
                            <?= empty($programs) ? 'disabled' : '' ?>>

                            Submit Application

                        </button>

                    </div>

                </form>

            </section>

        </div>

        <footer class="footer">
            Financial Aid System | Resident Portal
        </footer>

    </main>

</div>

</body>
</html>
