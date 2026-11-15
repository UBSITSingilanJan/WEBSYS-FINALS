<?php
require_once "../dbconn.php";
require_once "../classes/beneficiary.php";

$conn = new Beneficiary($pdo);

$search = trim($_GET['q'] ?? '');
$results = [];
$beneficiaries = [];

if ($search !== '') {
    $results = $conn->search_residents($search);
} else {
    $beneficiaries = $conn->approved_beneficiaries();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Residents — Worker</title>
    <link rel="stylesheet" href="worker_style.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="search.php" class="active">Search Residents</a>
        <a href="distributions.php">Distributions</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main-content">
        <div class="topbar"><h2>Search Residents</h2></div>

        <form method="get" action="search.php" class="search-form">
            <input type="text" name="q" class="search-input"
                   placeholder="Search by name, resident ID, address or contact number"
                   value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
            <button type="submit" class="search-btn">Search</button>
            <?php if ($search !== ''): ?>
                <a href="search.php" class="search-clear">Clear</a>
            <?php endif; ?>
        </form>

        <?php if ($search !== ''): ?>
            <section class="panel">
                <div class="panel-header">
                    <h3>Results for "<?php echo htmlspecialchars($search); ?>"</h3>
                </div>

                <?php if (empty($results)): ?>
                    <p class="empty-message">No resident matched your search.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="applicant-table">
                            <thead>
                                <tr>
                                    <th>Resident ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Aid Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($results as $fetch): ?>
                                <?php $received = $conn->has_received($fetch['resident_id']); ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fetch['resident_id']); ?></td>
                                    <td><?php echo htmlspecialchars(trim($fetch['firstname'] . ' ' . $fetch['middlename'] . ' ' . $fetch['lastname'])); ?></td>
                                    <td><?php echo htmlspecialchars($fetch['address']); ?></td>
                                    <td><?php echo htmlspecialchars($fetch['contact']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $received ? 'badge-yes' : 'badge-no'; ?>">
                                            <?php echo $received ? 'Already received aid' : 'No record yet'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="open-record-btn"
                                           href="resident_record.php?id=<?php echo urlencode($fetch['resident_id']); ?>">
                                           Open Record
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="panel">
                <div class="panel-header"><h3>Approved Beneficiaries</h3></div>

                <?php if (empty($beneficiaries)): ?>
                    <p class="empty-message">No approved beneficiaries yet.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="applicant-table">
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Aid Type</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($beneficiaries as $fetch): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(trim($fetch['firstname'] . ' ' . $fetch['middlename'] . ' ' . $fetch['lastname'])); ?></td>
                                    <td><?php echo htmlspecialchars($fetch['email']); ?></td>
                                    <td>
                                        <span class="benefit-badge">
                                            <?php echo htmlspecialchars($fetch['aid_type']); ?>
                                        </span>
                                    </td>
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