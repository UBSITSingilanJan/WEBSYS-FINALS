<?php
	require_once "../dbconn.php";
	require_once "../classes/applicant.php";

	$conn = new Applicant($pdo);

	if (isset($_POST['apply_btn'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$middlename = htmlspecialchars($_POST['middlename']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = $_POST['email'];
		$aid_type = $_POST['aid_type'];

		$conn -> insert_data($firstname, $middlename, $lastname, $email, $aid_type);

		header("Location: ../resident/index.php");
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
				<td><input type="email" name="email" placeholder="Email" required></td>
			</tr>
			<tr>
				<td>
					<select name="aid_type" required>
						<option value="Financial Aid">Financial</option>
						<option value="Scholarship">Scholarship</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><a href="../resident/index.php">Cancel</td>
				<td><button name="apply_btn">Save</td>
			</tr>
		</table>
	</form>
</body>
</html>