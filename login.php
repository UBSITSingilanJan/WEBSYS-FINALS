<?php
	session_start();
	require_once "dbconn.php";
	require_once "classes/users.php";
	require_once "classes/worker.php";


	if (isset($_POST['login_btn'])) {
		$email = isset($_POST['email']) && is_string($_POST['email'])
			? strtolower(trim($_POST['email']))
			: '';
		$password = isset($_POST['password']) && is_string($_POST['password'])
			? $_POST['password']
			: '';

		if($email === "admin@gmail.com" && password_verify($password, '$2y$12$sYR7I0Xgxb2zBlDAE74.XenPIqlObnJUhisbD8J5V.P/AzRGbDd1W')){
			session_regenerate_id(true);
			$_SESSION["role"]  = "admin";
			$_SESSION["ROLE"]  = "admin";
			$_SESSION["FNAME"] = "Admin";
			$_SESSION["LNAME"] = "Admin";

			header("Location: admin/index.php");
			exit();
		}

		$account = new User($pdo);
		$row = $account -> user_login($email, $password);

		if($row){
			session_regenerate_id(true);
			$_SESSION["user_id"] = $row['user_id'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];
			$_SESSION["role"]      = $row['role'] ?? 'resident';
			$_SESSION['must_change_password'] = !empty($row['must_change_password']);

			if ($_SESSION['must_change_password']) {
				header("Location: resident/change_password.php");
				exit();
			}

			header("Location: resident/index.php");
			exit();
		}

		$account = new User($pdo);
		$row = $account -> worker_login($email, $password);

		if($row){
			session_regenerate_id(true);
			$_SESSION["worker_id"] = $row['user_id'];
			$_SESSION["user_id"]   = $row['user_id'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];
			$_SESSION["role"]      = $row['role'] ?? 'worker';

			header("Location: worker/index.php");
			exit();
		}

		echo "<script>alert('Invalid credentials.'); window.location='index.php';</script>";
		exit;
	}
?>