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
</body>
</html>