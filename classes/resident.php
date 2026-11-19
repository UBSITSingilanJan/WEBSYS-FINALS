<?php
	class Resident{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function update_data($resident_id, $firstname, $middlename, $lastname, $suffix, $age, $gender, $contact, $email, $housenumber, $street, $barangay, $city, $province, $zipcode){
			$insert = "UPDATE resident_information SET firstname = :firstname, middlename = :middlename, lastname = :lastname, suffix = :suffix, age = :age, gender = :gender, contact = :contact, email = :email, housenumber = :housenumber, street = :street, barangay = :barangay, city = :city, province = :province, zipcode = :zipcode WHERE resident_id = :resident_id";

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":middlename" => $middlename,
				":lastname" => $lastname,
				":suffix" => $suffix,
				":age" => $age,
				":gender" => $gender,
				":contact" => $contact,
				":email" => $email,
				":housenumber" => $housenumber,
				":street" => $street,
				":barangay" => $barangay,
				":city" => $city,
				":province" => $province,
				":zipcode" => $zipcode,
				":resident_id" => $resident_id,
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


        public function count_residents() {

            $count = "SELECT COUNT(DISTINCT CONCAT(
                LOWER(TRIM(firstname)), '|',
                LOWER(TRIM(COALESCE(middlename, ''))), '|',
                LOWER(TRIM(lastname))
            )) AS total
            FROM resident_information";
            $prep = $this->pdo->prepare($count);
            $prep->execute();
            return $prep->fetch(PDO::FETCH_ASSOC);
        }

	}
?>