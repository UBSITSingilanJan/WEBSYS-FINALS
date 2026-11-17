
<?php
    require_once "../dbconn.php";
    require_once "../classes/worker.php";

    $conn = new Worker($pdo);
    $rows = $conn -> fetch_data();
    $totalWorkers = $conn -> count_workers();
<<<<<<< HEAD
=======

    $roleSummary = [];
    foreach ($rows as $row) {
        $position = $row['position'];

        if (!isset($roleSummary[$position])) {
            $roleSummary[$position] = 0;
        }

        $roleSummary[$position]++;
    }
>>>>>>> 52fad0d (fixed profile and modified application form)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>
<<<<<<< HEAD
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                Worker Management System
            </a>

            <a href="logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Welcome Admin</h2>
                <p class="text-muted mb-0">
                    Manage your registered workers here.
                </p>
            </div>

            <a href="add_worker.php" class="btn btn-success">
                + Add Worker
            </a>
        </div>
        <div class="row mb-4">

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <h6 class="text-muted">
                            Total Workers
                        </h6>

                        <h2 class="fw-bold text-primary mb-0">
                            <?php echo $totalWorkers['total']; ?>
                        </h2>

                        <small class="text-muted">
                            Registered worker accounts
                        </small>

                    </div>
                </div>
            </div>

        </div>
        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Worker List</h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle">

                        <thead class="table-dark">
=======
    <link rel="stylesheet" href="../worker/worker_style.css">
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
>>>>>>> 52fad0d (fixed profile and modified application form)
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Position</th>
<<<<<<< HEAD
                                <th class="text-center">Actions</th>
=======
                                <th>Actions</th>
>>>>>>> 52fad0d (fixed profile and modified application form)
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
<<<<<<< HEAD
                                        <span class="badge bg-primary">
=======
                                        <span class="benefit-badge">
>>>>>>> 52fad0d (fixed profile and modified application form)
                                            <?php echo htmlspecialchars($row['position']); ?>
                                        </span>
                                    </td>

<<<<<<< HEAD
                                    <td class="text-center">
                                        <a href="update.php?id=<?php echo urlencode($row['worker_id']); ?>"
                                           class="btn btn-warning btn-sm">
                                            Update
                                        </a>

                                        <a href="delete.php?id=<?php echo urlencode($row['worker_id']); ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this worker?');">
=======
                                    <td>
                                        <a href="update.php?id=<?php echo urlencode($row['user_id']); ?>"
                                           class="open-record-btn" style="margin-right: 6px;">
                                            Update
                                        </a>

                                        <a href="delete.php?id=<?php echo urlencode($row['user_id']); ?>"
                                           class="open-record-btn"
                                           onclick="return confirm('Are you sure you want to delete this worker?');"
                                           style="background: #dc2626;">
>>>>>>> 52fad0d (fixed profile and modified application form)
                                            Delete
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                            <?php if(empty($rows)){ ?>

                                <tr>
<<<<<<< HEAD
                                    <td colspan="7" class="text-center text-muted py-4">
=======
                                    <td colspan="7" class="empty-message">
>>>>>>> 52fad0d (fixed profile and modified application form)
                                        No workers found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

<<<<<<< HEAD
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
=======
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
>>>>>>> 52fad0d (fixed profile and modified application form)

</body>
</html>
