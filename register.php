<?php
	require_once "dbconn.php";
	require_once "classes/users.php";

	$conn = new User($pdo);

	if (isset($_POST['register_btn'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = $_POST['email'];
		$password = $_POST['password'];

		$conn -> insert_data($firstname, $lastname, $email, $password);

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

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

	<link rel="stylesheet" href="styles.css">
</head>
<body >
	<h1>Financial Aid System Registration</h1>
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
				<td><input type="password" name="password" placeholder="Password" required></td>
			</tr>
			<tr>
				<td><a href="index.php">Cancel</a></td>
				<td><button name="register_btn">Register</button></td>
			</tr>
		</table>
	</form>
</body>
</html>