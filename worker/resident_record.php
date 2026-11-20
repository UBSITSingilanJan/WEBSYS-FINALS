<?php
require_once "../dbconn.php";
require_once "../classes/beneficiary.php";

$conn = new Beneficiary($pdo);

$resident_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$resident = null;
$history = [];
$received = false;

if ($resident_id) {
    try {
        $resident = $conn->find_resident($resident_id);

        if ($resident) {
            $history = $conn->aid_history($resident['resident_id']);
            $received = $conn->has_received($resident['resident_id']);
        }
    } catch (Throwable $e) {
        $resident = null;
        $history = [];
        $received = false;
    }
}

$resident_name = '';
if (is_array($resident)) {
    $name_parts = [];
    foreach (['firstname', 'middlename', 'lastname'] as $field) {
        $value = $resident[$field] ?? '';
        if (trim((string) $value) !== '') {
            $name_parts[] = trim((string) $value);
        }
    }
    $resident_name = implode(' ', $name_parts);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resident Record</title>
    <link rel="stylesheet" href="../theme.css">
</head>
<body>
<div class="dashboard">

    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="hamburger">☰</label>
    
    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="search.php">Search Residents</a>
        <a href="distributions.php">Distributions</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <h2>Resident Record</h2>
            <button type="button" class="search-btn" onclick="window.print()">Print</button>
        </div>

        <?php if (!$resident): ?>
            <section class="panel"><p class="empty-message">Record not found.</p></section>
        <?php else: ?>
            <section class="panel">
                <div class="panel-header">
                    <h3>
                        <?php echo htmlspecialchars($resident_name ?: 'Resident'); ?>
                        <span class="badge <?php echo $received ? 'badge-yes' : 'badge-no'; ?>">
                            <?php echo $received ? 'Already received aid' : 'No record yet'; ?>
                        </span>
                    </h3>
                </div>

                <div class="table-wrapper">
                    <table class="applicant-table">
                        <tbody>
                            <tr><th>Resident ID</th><td><?php echo htmlspecialchars((string)($resident['resident_id'] ?? 'N/A')); ?></td></tr>
                            <tr><th>Age</th><td><?php echo htmlspecialchars((string)($resident['age'] ?? 'N/A')); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo htmlspecialchars((string)($resident['gender'] ?? 'N/A')); ?></td></tr>
                            <tr><th>Contact Number</th><td><?php echo htmlspecialchars((string)($resident['contact'] ?? 'N/A')); ?></td></tr>
                            <tr><th>Address</th><td><?php echo htmlspecialchars((string)($resident['address'] ?? 'N/A')); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><h3>Aid History</h3></div>

                <?php if (empty($history)): ?>
                    <p class="empty-message">No record yet.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="applicant-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Assistance Program</th>
                                    <th>Amount Released</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($history as $fetch): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)($fetch['release_date'] ?? 'N/A')); ?></td>
                                    <td><?php echo htmlspecialchars((string)($fetch['program'] ?? 'N/A')); ?></td>
                                    <td>₱<?php echo number_format((float)($fetch['amount'] ?? 0), 2); ?></td>
                                    <td><?php echo htmlspecialchars((string)($fetch['remarks'] ?? '—')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>