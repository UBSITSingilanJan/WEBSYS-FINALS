
<?php
    require_once "../dbconn.php";
    require_once "../classes/worker.php";

    $conn = new Worker($pdo);
    $rows = $conn -> fetch_data();
    $totalWorkers = $conn -> count_workers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>
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
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Position</th>
                                <th class="text-center">Actions</th>
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
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($row['position']); ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="update.php?id=<?php echo urlencode($row['worker_id']); ?>"
                                           class="btn btn-warning btn-sm">
                                            Update
                                        </a>

                                        <a href="delete.php?id=<?php echo urlencode($row['worker_id']); ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this worker?');">
                                            Delete
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                            <?php if(empty($rows)){ ?>

                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No workers found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
