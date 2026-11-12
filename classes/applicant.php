
<?php
    class Applicant{
        private PDO $pdo;

        function __construct(PDO $pdo){
            $this -> pdo = $pdo;
        }

        public function insert_data($firstname, $middlename, $lastname, $email, $aid_type, $user_id){
            $insert = "INSERT INTO application(firstname, middlename, lastname, email, aid_type, user_id, status)
                       VALUES(:firstname, :middlename, :lastname, :email, :aid_type, :user_id, :status)";
            $prep = $this -> pdo -> prepare($insert);
            $prep -> execute([
                ":firstname" => $firstname,
                ":middlename" => $middlename,
                ":lastname" => $lastname,
                ":email" => $email,
                ":aid_type" => $aid_type,
                ":user_id" => $user_id,
                ":status" => "Pending",
            ]);
        }

        public function fetch_data(){
            $fetch = "SELECT * FROM application";
            $prep = $this -> pdo -> prepare($fetch);
            $prep -> execute();
            return $prep -> fetchAll();
        }

        public function count_applicants(){
            $count = "SELECT COUNT(*) AS total FROM application";
            $prep = $this -> pdo -> prepare($count);
            $prep -> execute();
            return $prep -> fetch();
        }

        public function count_benefits(){

            $count = "SELECT COUNT(DISTINCT aid_type) AS total FROM application";
            $prep = $this -> pdo -> prepare($count);
            $prep -> execute();
            return $prep -> fetch();
        }


		public function fetch_resident_applications($user_id){

		    $fetch = "SELECT * FROM application
		              WHERE user_id = :user_id";
		    $prep = $this -> pdo -> prepare($fetch);
		    $prep -> execute([
		        ":user_id" => $user_id,
		    ]);
		    return $prep -> fetchAll();
		}
    }
?>
