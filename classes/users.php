<?php
	class User{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function insert_data($firstname, $lastname, $email, $password){
			$insert = "INSERT INTO user(firstname, lastname, email, password) VALUES(:firstname, :lastname, :email, :password)";

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":password" => $password,
			]);
		}

		public function user_login($email, $password) {
	        $fetch = "SELECT * FROM user WHERE email = :email";

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

	    public function worker_login($email, $password){
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