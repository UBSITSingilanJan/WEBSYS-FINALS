<?php
	require_once "../dbconn.php";
	require_once "../classes/worker.php";

	$conn = new Worker($pdo);

	$id = $_GET['id'];

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
				<td><input type="text" name="firstname" placeholder="Firstname" value="<?php echo $row['firstname']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" value="<?php echo $row['lastname']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="email" name="email" placeholder="Email" value="<?php echo $row['email']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="tel" name="contact" placeholder="Contact Number" value="<?php echo $row['contact']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" value="<?php echo $row['password']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="text" name="address" placeholder="Address" value="<?php echo $row['address']; ?>" required></td>
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
				<td><a href="index.php">Cancel</a></td>
				<td><button name="edit_worker">Update</button></td>
				<td><input type="hidden" name="worker_id" value="<?php echo $row['worker_id']; ?>"></td>
			</tr>
		</table>
	</form>
</body>
</html>