<?php
	require_once "../dbconn.php";
	require_once "../classes/worker.php";

	$conn = new Worker($pdo);

	$id = $_GET['id'] ?? null;

	if (!$id) {
		header("Location: index.php");
		exit();
	}

	$row = $conn -> show_update_data($id);

	if (isset($_POST['edit_worker'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = $_POST['email'];
		$contact = $_POST['contact'];
		$password = $_POST['password'];
		$address = $_POST['address'];
		$position = $_POST['position'];
		$worker_id = $_POST['worker_id'];

		$conn -> update_data($worker_id, $firstname, $lastname, $email, $contact, $password, $address, $position);
		
		header("Location: index.php");
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Worker</title>
	<link rel="stylesheet" href="../theme.css">
</head>
<body >
	<div class="dashboard">

		<input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    	<label for="sidebar-toggle" class="hamburger">☰</label>
		
		<aside class="sidebar">
			<h3>Financial Aid System</h3>
			<a href="index.php">Dashboard</a>
			<a href="add_worker.php">Add Worker</a>
			<a href="logout.php">Logout</a>
		</aside>

		<main class="main-content">
			<div class="topbar">
				<h2>Update Worker</h2>
				<a href="index.php" class="search-clear">Back to Dashboard</a>
			</div>

			<div class="form-shell">
				<div class="form-card">
					<div class="form-header">
						<h3>Edit Worker Profile</h3>
						<p>Update the worker information below.</p>
					</div>

					<form method="post">
						<div class="form-grid">
							<div class="form-field">
								<label for="firstname">Firstname</label>
								<input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
							</div>

							<div class="form-field">
								<label for="lastname">Lastname</label>
								<input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
							</div>

							<div class="form-field full">
								<label for="email">Email</label>
								<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
							</div>

							<div class="form-field">
								<label for="contact">Contact Number</label>
								<input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" required>
							</div>

							<div class="form-field">
								<label for="password">Password</label>
								<input type="password" id="password" name="password" value="<?php echo htmlspecialchars($row['password']); ?>" required>
							</div>

							<div class="form-field full">
								<label for="address">Address</label>
								<input type="text" id="address" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
							</div>

							<div class="form-field full">
								<label for="position">Position</label>
								<select id="position" name="position" required>
									<option value="Barangay Captain" <?php echo ($row['position'] == 'Barangay Captain') ? 'selected' : ''; ?>>Barangay Captain</option>
									<option value="Kagawad" <?php echo ($row['position'] == 'Kagawad') ? 'selected' : ''; ?>>Kagawad</option>
									<option value="Secretary" <?php echo ($row['position'] == 'Secretary') ? 'selected' : ''; ?>>Secretary</option>
								</select>
							</div>
						</div>

						<div class="form-actions">
							<a href="index.php" class="form-btn-secondary">Cancel</a>
							<button type="submit" name="edit_worker" class="form-btn">Update Worker</button>
							<input type="hidden" name="worker_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
						</div>
					</form>
				</div>
			</div>
		</main>
	</div>
</body>
</html>