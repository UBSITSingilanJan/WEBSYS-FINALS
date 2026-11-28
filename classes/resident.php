<?php
	class Resident{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function update_data($resident_id, $firstname, $middlename, $lastname, $suffix, $age, $gender, $contact, $email, $housenumber, $street, $barangay, $city, $province, $zipcode){
			$optional = function ($value) {
				return trim((string)$value) !== '' ? $value : null;
			};
			$address = implode(', ', array_filter([
				$housenumber, $street, $barangay, $city, $province
			], function ($part) {
				return trim((string)$part) !== '';
			}));
			$insert = "UPDATE resident_information
				SET firstname = :firstname, middlename = :middlename, lastname = :lastname,
					suffix = :suffix, age = :age, gender = :gender, contact = :contact,
					email = :email, housenumber = :housenumber, street = :street,
					barangay = :barangay, city = :city, province = :province,
					zipcode = :zipcode, address = :address
				WHERE resident_id = :resident_id";

			$prep = $this -> pdo -> prepare($insert);
			$prep -> execute([
				":firstname" => $firstname,
				":middlename" => $optional($middlename),
				":lastname" => $lastname,
				":suffix" => $optional($suffix),
				":age" => $optional($age),
				":gender" => $optional($gender),
				":contact" => $optional($contact),
				":email" => $optional($email),
				":housenumber" => $optional($housenumber),
				":street" => $optional($street),
				":barangay" => $optional($barangay),
				":city" => $optional($city),
				":province" => $optional($province),
				":zipcode" => $optional($zipcode),
				":address" => $address !== '' ? $address : null,
				":resident_id" => $resident_id,
			]);
		}

		public function create_resident(array $resident, ?int $userId = null){
			$fields = $this->profile_fields();
			if ($userId !== null) {
				array_unshift($fields, 'user_id');
				$resident['user_id'] = $userId;
			}
			$columns = implode(', ', array_map(function ($field) {
				return '`' . $field . '`';
			}, $fields));
			$placeholders = implode(', ', array_map(function ($field) {
				return ':' . $field;
			}, $fields));
			$insert = "INSERT INTO resident_information ($columns) VALUES ($placeholders)";
			$prep = $this->pdo->prepare($insert);
			$parameters = $this->resident_parameters($resident);
			if ($userId !== null) {
				$parameters[':user_id'] = $userId;
			}
			$prep->execute($parameters);
			return (int)$this->pdo->lastInsertId();
		}

		public function update_resident($resident_id, array $resident){
			$fields = $this->profile_fields();
			$assignments = array_map(function ($field) {
				return '`' . $field . '` = :' . $field;
			}, $fields);
			$params = $this->resident_parameters($resident);
			$params[':resident_id'] = $resident_id;
			$update = $this->pdo->prepare(
				'UPDATE resident_information SET ' . implode(', ', $assignments) . ' WHERE resident_id = :resident_id'
			);
			$update->execute($params);
		}

		public function update_profile($user_id, array $resident){
			$address = implode(', ', array_filter([
				$resident['housenumber'], $resident['street'], $resident['barangay'],
				$resident['city'], $resident['province']
			], function ($part) {
				return trim((string)$part) !== '';
			}));
			$fields = [
				'firstname', 'middlename', 'lastname', 'suffix', 'nickname',
				'birthdate', 'residency_type', 'move_in_date', 'age', 'gender', 'civilstatus', 'contact', 'email',
				'housenumber', 'street', 'barangay', 'city', 'province', 'zipcode',
				'address', 'birthplace', 'occupation', 'monthly_income', 'education',
				'course', 'talents', 'marketable_skills', 'health_condition',
				'transport_mode', 'transport_details', 'soloparent', 'children',
				'member', 'program', 'dependent', 'relationship', 'benefits',
				'school', 'yearlevel', 'enrolled', 'attained', 'senior', 'issues'
			];
			$assignments = array_map(function ($field) {
				return $field . ' = :' . $field;
			}, $fields);
			$params = [];
			foreach ($fields as $field) {
				$value = $field === 'address' ? $address : ($resident[$field] ?? '');
				$params[':' . $field] = trim((string)$value) !== '' ? $value : null;
			}
			$params[':user_id'] = $user_id;

			$updateResident = $this->pdo->prepare(
				'UPDATE resident_information SET ' . implode(', ', $assignments) . ' WHERE user_id = :user_id'
			);
			$updateResident->execute($params);

			$updateUser = $this->pdo->prepare(
				'UPDATE user SET firstname = :firstname, lastname = :lastname WHERE user_id = :user_id'
			);
			$updateUser->execute([
				':firstname' => $resident['firstname'],
				':lastname' => $resident['lastname'],
				':user_id' => $user_id,
			]);
		}

		private function profile_fields(){
			return [
				'firstname', 'middlename', 'lastname', 'suffix', 'nickname',
				'birthdate', 'residency_type', 'move_in_date', 'age', 'gender', 'civilstatus', 'contact', 'email',
				'housenumber', 'street', 'barangay', 'city', 'province', 'zipcode',
				'address', 'birthplace', 'occupation', 'monthly_income', 'education',
				'course', 'talents', 'marketable_skills', 'health_condition',
				'transport_mode', 'transport_details', 'soloparent', 'children',
				'member', 'program', 'dependent', 'relationship', 'benefits',
				'school', 'yearlevel', 'enrolled', 'attained', 'senior', 'issues'
			];
		}

		private function resident_parameters(array $resident){
			$parameters = [];
			foreach ($this->profile_fields() as $field) {
				$value = $resident[$field] ?? '';
				$parameters[':' . $field] = trim((string)$value) !== '' ? $value : null;
			}
			return $parameters;
		}

		public function find_resident($resident_id){
			$prep = $this->pdo->prepare("SELECT * FROM resident_information WHERE resident_id = ?");
			$prep->execute([$resident_id]);
			return $prep->fetch(PDO::FETCH_ASSOC);
		}

		public function search_residents($search = ''){
			$sql = "SELECT ri.*,
					EXISTS (
						SELECT 1
						FROM application a
						INNER JOIN assistance_release release_record
							ON release_record.applicant_id = a.applicant_id
						WHERE a.resident_id = ri.resident_id
					) AS has_received_aid
				FROM resident_information ri";
			$params = [];
			if (trim($search) !== '') {
				$sql .= " WHERE CONCAT_WS(' ', ri.firstname, ri.middlename, ri.lastname, ri.suffix) LIKE :name
					OR CAST(ri.resident_id AS CHAR) LIKE :resident_id
					OR ri.contact LIKE :contact
					OR ri.email LIKE :email
					OR ri.address LIKE :address";
				$keyword = '%' . trim($search) . '%';
				$params = [
					':name' => $keyword,
					':resident_id' => $keyword,
					':contact' => $keyword,
					':email' => $keyword,
					':address' => $keyword,
				];
			}
			$sql .= " ORDER BY ri.lastname, ri.firstname, ri.resident_id DESC";
			$prep = $this->pdo->prepare($sql);
			$prep->execute($params);
			return $prep->fetchAll(PDO::FETCH_ASSOC);
		}

		public function find_possible_duplicates(array $resident, $exclude_resident_id = null){
			$sql = "SELECT resident_id, firstname, middlename, lastname, contact, address
				FROM resident_information
				WHERE LOWER(TRIM(firstname)) = LOWER(TRIM(:firstname))
					AND LOWER(TRIM(lastname)) = LOWER(TRIM(:lastname))
					AND (
						(:contact <> '' AND (contact = :contact_match OR contact IS NULL OR contact = ''))
						OR (:address <> '' AND (
							LOWER(TRIM(address)) = LOWER(TRIM(:address_match))
							OR address IS NULL OR address = ''
						))
						OR (:contact_blank = '1' AND :address_blank = '1')
					)";
			$params = [
				':firstname' => $resident['firstname'],
				':lastname' => $resident['lastname'],
				':contact' => $resident['contact'],
				':contact_match' => $resident['contact'],
				':address' => $resident['address'],
				':address_match' => $resident['address'],
				':contact_blank' => $resident['contact'] === '' ? '1' : '0',
				':address_blank' => $resident['address'] === '' ? '1' : '0',
			];
			if ($exclude_resident_id !== null) {
				$sql .= " AND resident_id <> :exclude_resident_id";
				$params[':exclude_resident_id'] = $exclude_resident_id;
			}
			$sql .= " ORDER BY resident_id";
			$prep = $this->pdo->prepare($sql);
			$prep->execute($params);
			return $prep->fetchAll(PDO::FETCH_ASSOC);
		}

		public function show_update_data($user_id){
		    $fetch = "SELECT * FROM resident_information WHERE user_id = ?";

		    $prep = $this->pdo->prepare($fetch);
		    $prep->execute([$user_id]);

		    return $prep->fetch();
		}

		public function fetch_data(){
			$fetch = "SELECT * FROM resident_information";

			$prep = $this -> pdo -> prepare($fetch);
			$prep -> execute();
			return $prep -> fetchAll();
		}


        public function count_residents() {

            $count = "SELECT COUNT(*) AS total FROM resident_information";
            $prep = $this->pdo->prepare($count);
            $prep->execute();
            return $prep->fetch(PDO::FETCH_ASSOC);
        }

	}
?>