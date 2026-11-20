
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

        private function passwordMatches($password, $storedPassword){
            return password_verify($password, $storedPassword)
                || hash_equals($storedPassword, $password);
        }

        public function insert_data(array $resident, $email, $password){
            $email = trim($email);
            $firstname = trim($resident['firstname']);
            $lastname = trim($resident['lastname']);
            $address = implode(', ', array_filter([
                $resident['housenumber'], $resident['street'], $resident['barangay'],
                $resident['city'], $resident['province']
            ], function ($part) {
                return trim((string)$part) !== '';
            }));
            $fields = [
                'firstname', 'middlename', 'lastname', 'suffix', 'nickname',
                'birthdate', 'age', 'gender', 'civilstatus', 'contact', 'email',
                'housenumber', 'street', 'barangay', 'city', 'province', 'zipcode',
                'address', 'birthplace', 'occupation', 'monthly_income', 'education',
                'course', 'talents', 'marketable_skills', 'health_condition',
                'transport_mode', 'transport_details', 'soloparent', 'children',
                'member', 'program', 'dependent', 'relationship', 'benefits',
                'school', 'yearlevel', 'enrolled', 'attained', 'senior', 'issues'
            ];
            $resident['email'] = $email;
            $resident['address'] = $address;
            $columns = implode(', ', array_map(function ($field) {
                return '`' . $field . '`';
            }, array_merge(['user_id'], $fields)));
            $placeholders = implode(', ', array_fill(0, count($fields) + 1, '?'));

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
                    ":password" => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $user_id = $this -> pdo -> lastInsertId();

                $insert_resident = "INSERT INTO resident_information ($columns) VALUES ($placeholders)";
                $prep_resident = $this -> pdo -> prepare($insert_resident);
                $values = [(int)$user_id];
                foreach ($fields as $field) {
                    $value = $resident[$field] ?? '';
                    $values[] = trim((string)$value) !== '' ? $value : null;
                }
                $prep_resident->execute($values);

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

            if($row && $this -> passwordMatches($password, $row['password'])){
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

            if($row && $this -> passwordMatches($password, $row['password'])){
                return $row;
            }
            return false;
        }
    }
?>
