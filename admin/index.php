
<?php
    require_once "../dbconn.php";
    require_once "../classes/worker.php";

    $conn = new Worker($pdo);
    $rows = $conn -> fetch_data();
    $totalWorkers = $conn -> count_workers();

    $roleSummary = [];
    foreach ($rows as $row) {
        $position = $row['position'];

        if (!isset($roleSummary[$position])) {
            $roleSummary[$position] = 0;
        }

        $roleSummary[$position]++;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_styles.css">
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="index.php" class="active">Dashboard</a>
        <a href="add_worker.php">Add Worker</a>
        <a href="logout.php">Logout</a>

    </aside>

    <main class="main-content">
        <div class="topbar">

            <h2>Admin Dashboard</h2>
            <a href="add_worker.php" class="search-clear">+ Add Worker</a>

        </div>

        <div class="stats-container">
            <div class="stat-card">
                <p>Total Workers</p>

                <h1>
                    <?php echo (int)($totalWorkers['total'] ?? 0); ?>
                </h1>
            </div>

            <div class="stat-card" id="benefits">
                <p>Worker Roles</p>

                <h1>
                    <?php echo count($roleSummary); ?>
                </h1>
            </div>
        </div>

        <div class="content-grid">
            <section class="panel" id="applicants">

                <div class="panel-header">
                    <h3>Worker List</h3>
                </div>

                <div class="table-wrapper">

                    <table class="applicant-table">

                        <thead>
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Position</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($rows as $row){ ?>

                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['firstname']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['lastname']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['contact']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['address']); ?>
                                    </td>

                                    <td>
                                        <span class="benefit-badge">
                                            <?php echo htmlspecialchars($row['position']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="update.php?id=<?php echo urlencode($row['user_id']); ?>"
                                           class="open-record-btn" style="margin-right: 6px;">
                                            Update
                                        </a>

                                        <a href="delete.php?id=<?php echo urlencode($row['user_id']); ?>"
                                           class="open-record-btn"
                                           onclick="return confirm('Are you sure you want to delete this worker?');"
                                           style="background: #dc2626;">
                                            Delete
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                            <?php if(empty($rows)){ ?>

                                <tr>
                                    <td colspan="7" class="empty-message">
                                        No workers found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="panel">

                <h3>Roles Overview</h3>

                <div class="recent-list">

                    <?php if(!empty($roleSummary)){ ?>

                        <?php foreach($roleSummary as $role => $count){ ?>

                            <div class="recent-item">
                                <strong>
                                    <?php echo htmlspecialchars($role); ?>
                                </strong>

                                <small>
                                    <?php echo $count; ?> assigned worker(s)
                                </small>

                                <br>

                                <span class="benefit-badge">
                                    <?php echo $count; ?> Staff
                                </span>
                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <p class="empty-message">
                            No worker roles available.
                        </p>

                    <?php } ?>

                </div>

            </section>

        </div>
    </main>

</div>

</body>
</html>
