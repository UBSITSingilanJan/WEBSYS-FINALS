<?php
	class Applicant{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function insert_data($firstname, $middlename, $lastname, $email, $aid_type){
			$insert = "INSERT INTO application(firstname, middlename, lastname, email, aid_type) VALUES(:firstname, :middlename, :lastname, :email, :aid_type)";

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":middlename" => $middlename,
				":lastname" => $lastname,
				":email" => $email,
				":aid_type" => $aid_type,
			]);
		}

		public function fetch_data(){
			$fetch = "SELECT * FROM application";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}
	}
?>