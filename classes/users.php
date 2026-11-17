<<<<<<< HEAD
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
=======

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

        public function insert_data($firstname, $lastname, $email, $password){
            $email = trim($email);
            $firstname = trim($firstname);
            $lastname = trim($lastname);

            try {
                $this -> pdo -> beginTransaction();

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

                $user_id = $this -> pdo -> lastInsertId();

                $insert_resident = "INSERT INTO resident_information (user_id) VALUES (?)";

                $prep_resident = $this -> pdo -> prepare($insert_resident);
                $prep_resident -> execute([$user_id]);

                $this -> pdo -> commit();

                return true;

            } catch (PDOException $e) {
                $this -> pdo -> rollBack();
                throw $e;
            }
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

            if($row && $password == $row['password']){
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

            if($row && $password == $row['password']){
                return $row;
            }
            return false;
        }
    }
?>
>>>>>>> 52fad0d (fixed profile and modified application form)
