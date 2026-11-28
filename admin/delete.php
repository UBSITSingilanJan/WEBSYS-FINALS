<?php
	require_once "../dbconn.php";
	require_once "../classes/worker.php";

	$conn = new Worker($pdo);

	$id = $_GET['id'];

	$conn -> delete_data($id);

	header("Location: index.php");
	exit();
?>