<?php
	class Resident{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

<<<<<<< HEAD
		public function insert_data($firstname, $middlename, $lastname, $age, $gender, $contact, $address){
			$insert = "INSERT INTO resident_information(firstname, middlename, lastname, age, gender, contact, address) VALUES(:firstname, :middlename, :lastname, :age, :gender, :contact, :address)";
=======
		public function update_data($resident_id, $firstname, $middlename, $lastname, $suffix, $age, $gender, $contact, $email, $housenumber, $street, $barangay, $city, $province, $zipcode){
			$insert = "UPDATE resident_information SET firstname = :firstname, middlename = :middlename, lastname = :lastname, suffix = :suffix, age = :age, gender = :gender, contact = :contact, email = :email, housenumber = :housenumber, street = :street, barangay = :barangay, city = :city, province = :province, zipcode = :zipcode WHERE resident_id = :resident_id";
>>>>>>> 52fad0d (fixed profile and modified application form)

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":middlename" => $middlename,
				":lastname" => $lastname,
<<<<<<< HEAD
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
=======
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

		public function show_update_data($user_id){
		    $fetch = "SELECT * FROM resident_information WHERE user_id = ?";

		    $prep = $this->pdo->prepare($fetch);
		    $prep->execute([$user_id]);

		    return $prep->fetch();
>>>>>>> 52fad0d (fixed profile and modified application form)
		}

		public function fetch_data(){
			$fetch = "SELECT * FROM resident_information";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}
<<<<<<< HEAD
=======


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

>>>>>>> 52fad0d (fixed profile and modified application form)
	}
?>