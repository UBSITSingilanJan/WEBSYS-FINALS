<?php
require_once "../dbconn.php";
require_once "../classes/beneficiary.php";

$conn = new Beneficiary($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $distribution_id = filter_input(INPUT_POST, 'distribution_id', FILTER_VALIDATE_INT);
    $received_by = trim($_POST['received_by'] ?? '');
    $ack = isset($_POST['acknowledged']) ? 1 : 0;
    $released_by = $_SESSION['worker_name'] ?? '';

    if ($distribution_id && $received_by !== '') {
        $prep = $pdo->prepare(
            "UPDATE aid_distribution
             SET status = 'Released',
                 released_by = :released_by,
                 received_by = :received_by,
                 release_date = COALESCE(release_date, CURDATE()),
                 acknowledged = :ack
             WHERE distribution_id = :id"
        );
        $prep->execute([
            ':released_by' => $released_by,
            ':received_by' => $received_by,
            ':ack'         => $ack,
            ':id'          => $distribution_id,
        ]);
    }

    header('Location: distributions.php');
    exit;
}

$prep = $pdo->prepare(
    "SELECT d.*, r.firstname, r.middlename, r.lastname
     FROM aid_distribution d
     LEFT JOIN resident_information r ON r.resident_id = d.resident_id
     WHERE d.status IS NULL OR d.status <> 'Released'
     ORDER BY d.schedule_date ASC, d.distribution_id ASC"
);
$prep->execute();
$pending = $prep->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Distributions — Worker</title>
    <link rel="stylesheet" href="worker_style.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="search.php">Search Residents</a>
        <a href="distributions.php" class="active">Distributions</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main-content">
        <div class="topbar"><h2>Release Verification &amp; Acknowledgment</h2></div>

        <?php if (empty($pending)): ?>
            <section class="panel"><p class="empty-message">No distributions awaiting release.</p></section>
        <?php else: ?>
            <section class="panel">
                <div class="table-wrapper">
                    <table class="applicant-table">
                        <thead>
                            <tr>
                                <th>Resident</th>
                                <th>Program</th>
                                <th>Schedule</th>
                                <th>Amount / Goods</th>
                                <th>Mode</th>
                                <th>Received By</th>
                                <th>Acknowledged</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pending as $fetch): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(trim($fetch['firstname'] . ' ' . $fetch['middlename'] . ' ' . $fetch['lastname'])); ?></td>
                                <td><?php echo htmlspecialchars($fetch['program']); ?></td>
                                <td><?php echo htmlspecialchars($fetch['schedule_date']); ?></td>
                                <td><?php echo htmlspecialchars($fetch['amount_or_goods']); ?></td>
                                <td><?php echo htmlspecialchars($fetch['mode_of_receipt']); ?></td>

                                <form method="post" style="display:contents">
                                    <input type="hidden" name="distribution_id"
                                           value="<?php echo (int)$fetch['distribution_id']; ?>">
                                    <td>
                                        <input type="text" name="received_by" class="search-input"
                                               style="width:150px"
                                               value="<?php echo htmlspecialchars($fetch['received_by']); ?>"
                                               placeholder="Received by">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="acknowledged"
                                            <?php echo $fetch['acknowledged'] ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <button type="submit" class="open-record-btn">Mark Released</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>