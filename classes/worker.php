<?php
	class Worker{
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

		public function insert_data($firstname, $lastname, $email, $contact, $password, $address, $position){
			$firstname = trim($firstname);
			$lastname = trim($lastname);
			$email = trim($email);

			if ($this -> hasColumn('user', 'role')) {
				$insert = "INSERT INTO user(firstname, lastname, email, contact, password, address, position, role)
					VALUES(:firstname, :lastname, :email, :contact, :password, :address, :position, 'worker')";
			} else {
				$insert = "INSERT INTO user(firstname, lastname, email, contact, password, address, position)
					VALUES(:firstname, :lastname, :email, :contact, :password, :address, :position)";
			}

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":contact" => $contact,
				":password" => $password,
				":address" => $address,
				":position" => $position,
			]);
		}

		public function fetch_data(){
			$fetch = "SELECT * FROM user WHERE LOWER(role) = 'worker' ORDER BY user_id ASC";

			if (!$this -> hasColumn('user', 'role')) {
				$fetch = "SELECT * FROM user ORDER BY user_id ASC";
			}

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}

		public function delete_data($user_id){
			$delete = "DELETE FROM user WHERE user_id = :user_id AND role = 'worker'";
			if (!$this -> hasColumn('user', 'role')) {
				$delete = "DELETE FROM user WHERE user_id = :user_id";
			}

			$prep = $this -> pdo -> prepare($delete);
			return $prep -> execute([
				":user_id" => $user_id,
			]);
		}

		public function show_update_data($user_id){
			$fetch = "SELECT * FROM user WHERE user_id = :user_id AND role = 'worker'";
			if (!$this -> hasColumn('user', 'role')) {
				$fetch = "SELECT * FROM user WHERE user_id = :user_id";
			}

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute([
				":user_id" => $user_id,
			]);
			return $prep -> fetch();
		}

		public function update_data($user_id, $firstname, $lastname, $email, $contact, $password, $address, $position){
			$update = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE user_id = :user_id AND role = 'worker'";
			if (!$this -> hasColumn('user', 'role')) {
				$update = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE user_id = :user_id";
			}

			$prep = $this -> pdo -> prepare($update);
			return $prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":contact" => $contact,
				":password" => $password,
				":address" => $address,
				":position" => $position,
				":user_id" => $user_id,
			]);
		}

		public function login($email, $password){
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

	        if($row && $password == $row['password']){
	            return $row;
	        }
	        return false;
	    }

	    public function count_workers(){
    		$count = "SELECT COUNT(*) AS total FROM user WHERE role = 'worker'";
    		if (!$this -> hasColumn('user', 'role')) {
    			$count = "SELECT COUNT(*) AS total FROM user";
    		}
    		$prep = $this -> pdo -> prepare($count);
    		$prep -> execute();
    		return $prep -> fetch();
	}
	}
?>