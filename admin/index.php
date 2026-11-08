<?php
	require_once "../dbconn.php";
	require_once "../classes/worker.php";

	$conn = new Worker($pdo);
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
	<h1>Welcome admin</h1>

	<a href="add_worker.php">+ Add Worker | </a>
	<a href="logout.php">Logout</a>

	<table border="1">
		<tr>
			<th>Firstname</th>
			<th>Lastname</th>
			<th>Email</th>
			<th>Contact Number</th>
			<th>Address</th>
			<th>Position</th>
			<th>Actions</th>
		</tr>
		<?php
			foreach($rows as $row){
		?>
			<tr>
				<td><?php echo $row['firstname']; ?></td>
				<td><?php echo $row['lastname']; ?></td>
				<td><?php echo $row['email']; ?></td>
				<td><?php echo $row['contact']; ?></td>
				<td><?php echo $row['address']; ?></td>
				<td><?php echo $row['position']; ?></td>
				<td>
					<a href="update.php?id=<?php echo $row['worker_id']; ?>">Update | </a>
					<a href="delete.php?id=<?php echo $row['worker_id']; ?>">Delete</a>
				</td>
			</tr>
		<?php
			}
		?>
	</table>
</body>
</html>