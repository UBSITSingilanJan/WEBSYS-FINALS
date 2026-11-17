<?php
	require_once "../dbconn.php";
	require_once "../classes/worker.php";

	$conn = new Worker($pdo);

	if (isset($_POST['add_worker'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = $_POST['email'];
		$contact = $_POST['contact'];
		$password = $_POST['password'];
		$address = $_POST['address'];
		$position = $_POST['position'];

		$conn -> insert_data($firstname, $lastname, $email, $contact, $password, $address, $position);
		
		header("Location: index.php");
		exit();
	}
?>
<!DOCTYPE html>
<<<<<<< HEAD
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<form method="post">
		<table>
			<tr>
				<td><input type="text" name="firstname" placeholder="Firstname" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" required></td>
			</tr>
			<tr>
				<td><input type="email" name="email" placeholder="Email" required></td>
			</tr>
			<tr>
				<td><input type="tel" name="contact" placeholder="Contact Number" required></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" required></td>
			</tr>
			<tr>
				<td><input type="text" name="address" placeholder="Address" required></td>
			</tr>
			<tr>
				<td>
					<select name="position" required>
						<option value="Barangay Captain">Barangay Captain</option>
						<option value="Kagawad">Kagawad</option>
						<option value="Secretary">Secretary</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><a href="index.php">Cancel</td>
				<td><button name="add_worker">Add </td>
			</tr>
		</table>
	</form>
=======
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add Worker</title>
	<link rel="stylesheet" href="../worker/worker_style.css">
</head>
<body>
	<div class="dashboard">
		<aside class="sidebar">
			<h3>Financial Aid System</h3>
			<a href="index.php">Dashboard</a>
			<a href="add_worker.php" class="active">Add Worker</a>
			<a href="logout.php">Logout</a>
		</aside>

		<main class="main-content">
			<div class="topbar">
				<h2>Add Worker</h2>
				<a href="index.php" class="search-clear">Back to Dashboard</a>
			</div>

			<div class="form-shell">
				<div class="form-card">
					<div class="form-header">
						<h3>Register New Worker</h3>
						<p>Fill in the worker details below.</p>
					</div>

					<form method="post">
						<div class="form-grid">
							<div class="form-field">
								<label for="firstname">Firstname</label>
								<input type="text" id="firstname" name="firstname" placeholder="Enter firstname" required>
							</div>

							<div class="form-field">
								<label for="lastname">Lastname</label>
								<input type="text" id="lastname" name="lastname" placeholder="Enter lastname" required>
							</div>

							<div class="form-field full">
								<label for="email">Email</label>
								<input type="email" id="email" name="email" placeholder="Enter email address" required>
							</div>

							<div class="form-field">
								<label for="contact">Contact Number</label>
								<input type="tel" id="contact" name="contact" placeholder="Enter contact number" required>
							</div>

							<div class="form-field">
								<label for="password">Password</label>
								<input type="password" id="password" name="password" placeholder="Enter password" required>
							</div>

							<div class="form-field full">
								<label for="address">Address</label>
								<input type="text" id="address" name="address" placeholder="Enter home address" required>
							</div>

							<div class="form-field full">
								<label for="position">Position</label>
								<select id="position" name="position" required>
									<option value="" selected disabled>Select position</option>
									<option value="Barangay Captain">Barangay Captain</option>
									<option value="Kagawad">Kagawad</option>
									<option value="Secretary">Secretary</option>
								</select>
							</div>
						</div>

						<div class="form-actions">
							<a href="index.php" class="form-btn-secondary">Cancel</a>
							<button type="submit" name="add_worker" class="form-btn">Add Worker</button>
						</div>
					</form>
				</div>
			</div>
		</main>
	</div>
>>>>>>> 52fad0d (fixed profile and modified application form)
</body>
</html>