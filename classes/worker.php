<?php
	class Worker{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function insert_data($firstname, $lastname, $email, $contact, $password, $address, $position){
			$insert = "INSERT INTO worker(firstname, lastname, email, contact, password, address, position) VALUES(:firstname, :lastname, :email, :contact, :password, :address, :position)";

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
			$fetch = "SELECT * FROM worker";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}

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
			]);
			return $prep -> fetch();
		}

		public function update_data($worker_id, $firstname, $lastname, $email, $contact, $password, $address, $position){
			$update = "UPDATE worker SET firstname = :firstname, lastname = :lastname, email = :email, contact = :contact, password = :password, address = :address, position = :position WHERE worker_id = :worker_id";

			$prep = $this -> pdo -> prepare($update);
			return $prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":contact" => $contact,
				":password" => $password,
				":address" => $address,
				":position" => $position,
				":worker_id" => $worker_id,
			]);
		}

		public function login($email, $password){
	        $fetch = "SELECT * FROM worker WHERE email = :email";

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
	}
?>