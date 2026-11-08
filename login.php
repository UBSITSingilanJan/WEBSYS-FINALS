<?php
	session_start();
	require_once "dbconn.php";
	require_once "classes/users.php";
	require_once "classes/worker.php";


	if (isset($_POST['login_btn'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];

		if($email === "admin@gmail.com" && $password === "admin123"){
			$_SESSION["ROLE"]  = "admin";
			$_SESSION["FNAME"] = "Admin";
			$_SESSION["LNAME"] = "Admin";

			header("Location: admin/index.php");
			exit();
		}

		$account = new User($pdo);
		$row = $account -> user_login($email, $password);

		if($row){
			$_SESSION["user_id"] = $row['user_id'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];

			header("Location: resident/index.php");
			exit();
		}

		$account = new User($pdo);
		$row = $account -> worker_login($email, $password);

		if($row){
			$_SESSION["worker_id"] = $row['worker_id'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];

			header("Location: worker/index.php");
			exit();
		}

		echo "<script>alert('Invalid credentials.'); window.location='index.php';</script>";
		exit;
	}
?>