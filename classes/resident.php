<?php
	class Resident{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function insert_data($firstname, $middlename, $lastname, $age, $gender, $contact, $address){
			$insert = "INSERT INTO resident_information(firstname, middlename, lastname, age, gender, contact, address) VALUES(:firstname, :middlename, :lastname, :age, :gender, :contact, :address)";

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":middlename" => $middlename,
				":lastname" => $lastname,
				":age" => $age,
				":gender" => $gender,
				":contact" => $contact,
				":address" => $address,
			]);
		}

		public function show_update_data($resident_id){
			$fetch = "SELECT FROM resident_information WHERE resident_id = :resident_id";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetch();
		}

		public function fetch_data(){
			$fetch = "SELECT * FROM resident_information";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}
	}
?>