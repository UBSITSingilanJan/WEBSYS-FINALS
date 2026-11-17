<?php
	class Worker{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

<<<<<<< HEAD
		public function insert_data($firstname, $lastname, $email, $contact, $password, $address, $position){
			$insert = "INSERT INTO worker(firstname, lastname, email, contact, password, address, position) VALUES(:firstname, :lastname, :email, :contact, :password, :address, :position)";
=======
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
>>>>>>> 52fad0d (fixed profile and modified application form)

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
<<<<<<< HEAD
			$fetch = "SELECT * FROM worker";
=======
			$fetch = "SELECT * FROM user WHERE LOWER(role) = 'worker' ORDER BY user_id ASC";

			if (!$this -> hasColumn('user', 'role')) {
				$fetch = "SELECT * FROM user ORDER BY user_id ASC";
			}
>>>>>>> 52fad0d (fixed profile and modified application form)

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}

<<<<<<< HEAD
		public function delete_data($worker_id){
			$delete = "DELETE FROM worker WHERE worker_id = :worker_id";

			$prep = $this -> pdo -> prepare($delete);
			return $prep -> execute([
				":worker_id" => $worker_id,
			]);
		}

		public function show_update_data($worker_id){
			$fetch = "SELECT * FROM worker WHERE worker_id = :worker_id";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute([
				":worker_id" => $worker_id,
=======
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
>>>>>>> 52fad0d (fixed profile and modified application form)
			]);
			return $prep -> fetch();
		}

<<<<<<< HEAD
		public function update_data($worker_id, $firstname, $lastname, $email, $contact, $password, $address, $position){
			$update = "UPDATE worker SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE worker_id = :worker_id";
=======
		public function update_data($user_id, $firstname, $lastname, $email, $contact, $password, $address, $position){
			$update = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE user_id = :user_id AND role = 'worker'";
			if (!$this -> hasColumn('user', 'role')) {
				$update = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE user_id = :user_id";
			}
>>>>>>> 52fad0d (fixed profile and modified application form)

			$prep = $this -> pdo -> prepare($update);
			return $prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":contact" => $contact,
				":password" => $password,
				":address" => $address,
				":position" => $position,
<<<<<<< HEAD
				":worker_id" => $worker_id,
=======
				":user_id" => $user_id,
>>>>>>> 52fad0d (fixed profile and modified application form)
			]);
		}

		public function login($email, $password){
<<<<<<< HEAD
	        $fetch = "SELECT * FROM worker WHERE email = :email";
=======
	        $email = trim(strtolower($email));
	        $fetch = "SELECT * FROM user WHERE LOWER(email) = :email";

	        if ($this -> hasColumn('user', 'role')) {
	            $fetch .= " AND role = 'worker'";
	        }
>>>>>>> 52fad0d (fixed profile and modified application form)

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
<<<<<<< HEAD
    		$count = "SELECT COUNT(*) AS total FROM worker";
    		$prep = $this -> pdo -> prepare($count);
    		$prep -> execute();
    		return $prep -> fetch();
}
=======
    		$count = "SELECT COUNT(*) AS total FROM user WHERE role = 'worker'";
    		if (!$this -> hasColumn('user', 'role')) {
    			$count = "SELECT COUNT(*) AS total FROM user";
    		}
    		$prep = $this -> pdo -> prepare($count);
    		$prep -> execute();
    		return $prep -> fetch();
	}
>>>>>>> 52fad0d (fixed profile and modified application form)
	}
?>