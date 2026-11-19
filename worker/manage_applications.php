
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

// Escape output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Redirect while preserving search and optionally reopening a release form
function redirect_page($openApplicantId = null) {

    $search = trim($_POST['q'] ?? $_GET['q'] ?? "");
    $params = [];

    if ($search !== "") {
        $params['q'] = $search;
    }

    if ($openApplicantId !== null && $openApplicantId > 0) {
        $params['edit'] = $openApplicantId;
    }

    $query = !empty($params)
        ? "?" . http_build_query($params)
        : "";

    header("Location: manage_applications.php" . $query);
    exit();
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? "";

    $applicant_id = filter_var(
        $_POST['applicant_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$applicant_id || $applicant_id <= 0) {
        $_SESSION['flash'] = "Invalid applicant record.";
        redirect_page();
    }

    // UPDATE APPLICATION STATUS
    if ($action === "update_status") {

        $status = $_POST['status'] ?? "";

        if (!in_array($status, ["Pending", "Approved", "Rejected"], true)) {

            $_SESSION['flash'] = "Invalid application status.";

        } else {

            if ($manager->update_status($applicant_id, $status)) {
                $_SESSION['flash'] = "Application status updated successfully.";
            } else {
                $_SESSION['flash'] = "Unable to update application status.";
            }
        }

        redirect_page();
    }

    // CREATE FIRST RELEASE
    if ($action === "record_release") {

        $amount = $_POST['amount'] ?? "";
        $release_date = $_POST['release_date'] ?? "";
        $remarks = trim($_POST['remarks'] ?? "");

        $result = $manager->record_release(
            $applicant_id,
            $amount,
            $release_date,
            $remarks
        );

        $_SESSION['flash'] = $result['message'];

        // Reopen the release form after saving
        redirect_page($applicant_id);
    }

    // UPDATE EXISTING RELEASE
    if ($action === "update_release") {

        $release_id = filter_var(
            $_POST['release_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $amount = $_POST['amount'] ?? "";
        $release_date = $_POST['release_date'] ?? "";
        $remarks = trim($_POST['remarks'] ?? "");

        if (!$release_id || $release_id <= 0) {

            $_SESSION['flash'] = "Invalid release record.";

        } else {

            $result = $manager->update_release(
                $release_id,
                $applicant_id,
                $amount,
                $release_date,
                $remarks
            );

            $_SESSION['flash'] = $result['message'];
        }

        // Reopen the form with the updated information
        redirect_page($applicant_id);
    }

    $_SESSION['flash'] = "Invalid action.";

    redirect_page();
}

// GET DATA
$search = trim($_GET['q'] ?? "");

$openApplicantId = filter_var(
    $_GET['edit'] ?? null,
    FILTER_VALIDATE_INT
);

$applicants = $manager->fetch_applications($search);
$releases = $manager->fetch_recent_releases();
$counts = $manager->count_applications();

$flash = $_SESSION['flash'] ?? "";
unset($_SESSION['flash']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Applications</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="worker_style.css">
    <link rel="stylesheet" href="manage_applications.css">

</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="manage_applications.php" class="active">Manage Applications</a>
        <a href="manage_programs.php">Add Programs</a>
        <a href="logout.php">Logout</a>
    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="topbar">

            <div>

                <h2>Manage Applications</h2>

                <p class="page-subtitle">
                    Review applications and record financial assistance.
                </p>

            </div>

        </div>


        <!-- NOTIFICATION -->
        <?php if ($flash !== "") { ?>

            <div class="alert-message">
                <?php echo e($flash); ?>
            </div>

        <?php } ?>


        <!-- STATISTICS -->
        <div class="stats-container">

            <div class="stat-card">

                <p>Total Applicants</p>

                <h1>
                    <?php echo (int)($counts['total'] ?? 0); ?>
                </h1>

            </div>


            <div class="stat-card">

                <p>Pending Applications</p>

                <h1>
                    <?php echo (int)($counts['pending'] ?? 0); ?>
                </h1>

            </div>


            <div class="stat-card">

                <p>Approved Applications</p>

                <h1>
                    <?php echo (int)($counts['approved'] ?? 0); ?>
                </h1>

            </div>


            <div class="stat-card">

                <p>Rejected Applications</p>

                <h1>
                    <?php echo (int)($counts['rejected'] ?? 0); ?>
                </h1>

            </div>

        </div>


        <!-- APPLICANT RECORDS -->
        <section class="panel application-panel">

            <div class="panel-header">

                <h3>Applicant Records</h3>

            </div>


            <!-- SEARCH -->
            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="q"
                    class="search-input"
                    placeholder="Search applicant or program..."
                    value="<?php echo e($search); ?>"
                >

                <button type="submit" class="search-btn">
                    Search
                </button>

                <?php if ($search !== "") { ?>

                    <a href="manage_applications.php" class="search-clear">
                        Clear Search
                    </a>

                <?php } ?>

            </form>


            <div class="table-wrapper">

                <table class="applicant-table management-table">

                    <thead>

                        <tr>

                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Total Released</th>
                            <th>Update Status</th>
                            <th>Record Assistance</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (!empty($applicants)) { ?>

                            <?php foreach ($applicants as $applicant) { ?>

                                <?php

                                $hasRelease = !empty(
                                    $applicant['latest_release_id']
                                );

                                $statusClass = strtolower(
                                    $applicant['status']
                                );

                                $releaseDate = $hasRelease
                                    ? $applicant['latest_release_date']
                                    : date('Y-m-d');

                                ?>

                                <tr>

                                    <!-- APPLICANT NAME -->
                                    <td>

                                        <strong>

                                            <?php

                                            echo e(
                                                $applicant['firstname'] . " " .
                                                $applicant['middlename'] . " " .
                                                $applicant['lastname']
                                            );

                                            ?>

                                        </strong>

                                    </td>


                                    <!-- EMAIL -->
                                    <td>

                                        <?php echo e($applicant['email']); ?>

                                    </td>


                                    <!-- PROGRAM -->
                                    <td>

                                        <span class="benefit-badge">

                                            <?php echo e($applicant['aid_type']); ?>

                                        </span>

                                    </td>


                                    <!-- STATUS -->
                                    <td>

                                        <span class="status-badge status-<?php echo e($statusClass); ?>">

                                            <?php echo e($applicant['status']); ?>

                                        </span>

                                    </td>


                                    <!-- TOTAL RELEASED -->
                                    <td class="amount-cell">

                                        ₱<?php echo number_format(
                                            (float)$applicant['total_released'],
                                            2
                                        ); ?>

                                    </td>


                                    <!-- UPDATE STATUS -->
                                    <td>

                                        <form method="POST" class="action-form">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="update_status"
                                            >

                                            <input
                                                type="hidden"
                                                name="applicant_id"
                                                value="<?php echo (int)$applicant['applicant_id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="q"
                                                value="<?php echo e($search); ?>"
                                            >

                                            <select
                                                name="status"
                                                class="form-control status-select"
                                                required
                                            >

                                                <option
                                                    value="Pending"
                                                    <?php echo $applicant['status'] === "Pending" ? "selected" : ""; ?>
                                                >
                                                    Pending
                                                </option>

                                                <option
                                                    value="Approved"
                                                    <?php echo $applicant['status'] === "Approved" ? "selected" : ""; ?>
                                                >
                                                    Approved
                                                </option>

                                                <option
                                                    value="Rejected"
                                                    <?php echo $applicant['status'] === "Rejected" ? "selected" : ""; ?>
                                                >
                                                    Rejected
                                                </option>

                                            </select>


                                            <button
                                                type="submit"
                                                class="small-action-btn"
                                            >
                                                Save
                                            </button>

                                        </form>

                                    </td>


                                    <!-- ADD / EDIT RELEASE -->
                                    <td class="release-details">

                                        <?php if ($applicant['status'] === "Approved") { ?>

                                            <details
                                                <?php
                                                echo (
                                                    (int)$openApplicantId ===
                                                    (int)$applicant['applicant_id']
                                                ) ? "open" : "";
                                                ?>
                                            >

                                                <summary class="release-toggle">

                                                    <?php
                                                    echo $hasRelease
                                                        ? "Edit Release"
                                                        : "Add Release";
                                                    ?>

                                                </summary>


                                                <form
                                                    method="POST"
                                                    class="release-form"
                                                >

                                                    <!-- ACTION -->
                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="<?php echo $hasRelease ? 'update_release' : 'record_release'; ?>"
                                                    >


                                                    <!-- APPLICANT ID -->
                                                    <input
                                                        type="hidden"
                                                        name="applicant_id"
                                                        value="<?php echo (int)$applicant['applicant_id']; ?>"
                                                    >


                                                    <!-- RELEASE ID FOR UPDATING -->
                                                    <?php if ($hasRelease) { ?>

                                                        <input
                                                            type="hidden"
                                                            name="release_id"
                                                            value="<?php echo (int)$applicant['latest_release_id']; ?>"
                                                        >

                                                    <?php } ?>


                                                    <!-- SEARCH VALUE -->
                                                    <input
                                                        type="hidden"
                                                        name="q"
                                                        value="<?php echo e($search); ?>"
                                                    >


                                                    <!-- AMOUNT -->
                                                    <label>Amount (₱)</label>

                                                    <input
                                                        type="number"
                                                        name="amount"
                                                        min="0.01"
                                                        max="99999999.99"
                                                        step="0.01"
                                                        placeholder="Enter amount"
                                                        value="<?php
                                                            echo $hasRelease
                                                                ? e($applicant['latest_release_amount'])
                                                                : "";
                                                        ?>"
                                                        required
                                                    >


                                                    <!-- RELEASE DATE -->
                                                    <label>Release Date</label>

                                                    <input
                                                        type="date"
                                                        name="release_date"
                                                        value="<?php echo e($releaseDate); ?>"
                                                        required
                                                    >


                                                    <!-- REMARKS -->
                                                    <label>Remarks</label>

                                                    <textarea
                                                        name="remarks"
                                                        rows="2"
                                                        maxlength="255"
                                                        placeholder="Optional remarks"
                                                    ><?php
                                                        echo $hasRelease
                                                            ? e($applicant['latest_release_remarks'])
                                                            : "";
                                                    ?></textarea>


                                                    <!-- SUBMIT BUTTON -->
                                                    <button
                                                        type="submit"
                                                        class="release-submit"
                                                    >

                                                        <?php
                                                        echo $hasRelease
                                                            ? "Save Changes"
                                                            : "Save Release";
                                                        ?>

                                                    </button>

                                                </form>

                                            </details>

                                        <?php } else { ?>

                                            <span class="not-approved">
                                                Approve first
                                            </span>

                                        <?php } ?>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="7" class="empty-message">
                                    No applicants found.
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- RECENT ASSISTANCE RELEASES -->
        <section class="panel release-history-panel">

            <div class="panel-header">

                <h3>Recent Assistance Releases</h3>

            </div>


            <div class="table-wrapper">

                <table class="applicant-table">

                    <thead>

                        <tr>

                            <th>Applicant</th>
                            <th>Program</th>
                            <th>Amount Released</th>
                            <th>Release Date</th>
                            <th>Remarks</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (!empty($releases)) { ?>

                            <?php foreach ($releases as $release) { ?>

                                <tr>

                                    <td>

                                        <?php

                                        echo e(
                                            $release['firstname'] . " " .
                                            $release['lastname']
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <?php echo e($release['aid_type']); ?>

                                    </td>


                                    <td class="amount-cell">

                                        ₱<?php echo number_format(
                                            (float)$release['amount'],
                                            2
                                        ); ?>

                                    </td>


                                    <td>

                                        <?php echo e($release['release_date']); ?>

                                    </td>


                                    <td>

                                        <?php

                                        echo e(
                                            $release['remarks'] ?: "—"
                                        );

                                        ?>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="5" class="empty-message">

                                    No assistance releases recorded yet.

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- FOOTER -->
        <div class="footer">

            Financial Aid System | Worker Management

        </div>

    </main>

</div>

</body>
</html>
