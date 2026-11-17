<?php
	$host = "localhost";
	$user = "root";
	$pass = "";
<<<<<<< HEAD
	$db = "financialaidsystem";
=======
	$db = "financialaidsystem-new";
>>>>>>> 52fad0d (fixed profile and modified application form)
	$charset = "utf8mb4";

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$attributes = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];

	try{
		$pdo = new PDO($dsn, $user, $pass, $attributes);
	}
	catch(PDOException $e){
		die("Connection Failed" . $e -> getMessage());
	}
?>