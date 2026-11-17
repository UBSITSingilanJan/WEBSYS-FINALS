<?php
	class User{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		private function hasColumn($table, $column){
			try {
				$check = $this -> pdo -> query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
				return $check -> fetch() !== false;
			} catch (PDOException $e) {
				return false;
			}
		}

		private function passwordMatches($password, $storedPassword){
			return password_verify($password, $storedPassword)
				|| hash_equals($storedPassword, $password);
		}

		public function insert_data($firstname, $lastname, $email, $password){
			$email = trim($email);
			$firstname = trim($firstname);
			$lastname = trim($lastname);

			if ($this -> hasColumn('user', 'role')) {
				$insert = "INSERT INTO user(firstname, lastname, email, password, role) VALUES(:firstname, :lastname, :email, :password, 'resident')";
			} else {
				$insert = "INSERT INTO user(firstname, lastname, email, password) VALUES(:firstname, :lastname, :email, :password)";
			}

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":password" => $password,
			]);
		}

		public function user_login($email, $password) {
	        $email = trim(strtolower($email));
	        $fetch = "SELECT * FROM user WHERE LOWER(email) = :email";

	        if ($this -> hasColumn('user', 'role')) {
	            $fetch .= " AND role = 'resident'";
	        }

	        $prep = $this -> pdo ->prepare($fetch);
	        $prep -> execute([
	        	":email" => $email,
	        ]);
	        $row = $prep -> fetch();

	        if($row && $this -> passwordMatches($password, $row['password'])){
	            return $row;
	        }
	        return false;
	    }

	    public function worker_login($email, $password){
	        $email = trim(strtolower($email));
	        $fetch = "SELECT * FROM user WHERE LOWER(email) = :email";

	        if ($this -> hasColumn('user', 'role')) {
	            $fetch .= " AND role = 'worker'";
	        }

	        $prep = $this -> pdo ->prepare($fetch);
	        $prep -> execute([
	        	":email" => $email,
	        ]);
	        $row = $prep -> fetch();

	        if($row && $this -> passwordMatches($password, $row['password'])){
	            return $row;
	        }
	        return false;
	    }
	}
?>