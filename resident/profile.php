<?php
	require_once "../dbconn.php";
	require_once "../classes/resident.php";

	$conn = new Resident($pdo);

	if (isset($_POST['save_btn'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$middlename = htmlspecialchars($_POST['middlename']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$age = $_POST['age'];
		$gender = $_POST['gender'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];;

		$conn -> insert_data($firstname, $middlename, $lastname, $age, $gender, $contact, $address);

		header("Location: profile.php");
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
				<td><input type="text" name="middlename" placeholder="Middlename" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" required></td>
			</tr>
			<tr>
				<td><input type="number" name="age" placeholder="Age" required></td>
			</tr>
			<tr>
				<td>
					<select name="gender" required>
						<option value="Male">Male</option>
						<option value="Female">Female</option>
						<option value="Other">Other</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="text" name="contact" placeholder="Contact" required></td>
			</tr>
			<tr>
				<td><input type="text" name="address" placeholder="Address" required></td>
			</tr>
			<tr>
				<td><a href="index.php">Cancel</td>
				<td><button name="save_btn">Save</td>
			</tr>
		</table>
	</form>
</body>
</html>