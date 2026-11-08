<?php
	require_once "../dbconn.php";
	require_once "../classes/applicant.php";

	$conn = new Applicant($pdo);
	$rows = $conn -> fetch_data();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<h1>Welcome worker</h1>

	<a href="logout.php">Logout</a>

	<table border="1">
		<tr>
			<th>Firstname</th>
			<th>Middlename</th>
			<th>Lastname</th>
			<th>Email</th>
			<th>Benefit</th>
		</tr>
		<?php
			foreach($rows as $row){
		?>
			<tr>
				<td><?php echo $row['firstname']; ?></td>
				<td><?php echo $row['middlename']; ?></td>
				<td><?php echo $row['lastname']; ?></td>
				<td><?php echo $row['email']; ?></td>
				<td><?php echo $row['aid_type']; ?></td>
			</tr>
		<?php
			}
		?>
	</table>
</body>
</html>