
<?php
    class Applicant{
        private PDO $pdo;

        function __construct(PDO $pdo){
            $this -> pdo = $pdo;
        }

        private function format_selections($value){
            $decoded = json_decode($value ?? '', true);
            return is_array($decoded) ? implode(", ", $decoded) : (string)($value ?? '');
        }

        public function insert_data($firstname, $middlename, $lastname, $suffix, $contact, $email, $gender, $password, $civilstatus, $education, $soloparent, $children, $member, $program, $dependent, $relationship, $housenumber, $street, $barangay, $city, $province, $zipcode, $birthplace, $occupation, $school, $yearlevel, $course, $benefits, $enrolled, $attained, $senior, $issues, $documents){
            $insert = "INSERT INTO application(
                firstname, middlename, lastname, suffix, contact, email,
                gender, password, civilstatus, education, soloparent, children,
                member, program, dependent, relationship, housenumber,
                street, barangay, city, province, zipcode, birthplace,
                occupation, school, yearlevel, course, benefits, enrolled,
                attained, senior, documents, issues, status, role
            )
            VALUES(
                :firstname, :middlename, :lastname, :suffix, :contact, :email,
                :gender, :password, :civilstatus, :education, :soloparent, :children,
                :member, :program, :dependent, :relationship, :housenumber,
                :street, :barangay, :city, :province, :zipcode, :birthplace,
                :occupation, :school, :yearlevel, :course, :benefits, :enrolled,
                :attained, :senior, :documents, :issues, :status, 'resident'
            )";

            $prep = $this->pdo->prepare($insert);
            $prep->execute([
                ":firstname" => $firstname,
                ":middlename" => $middlename,
                ":lastname" => $lastname,
                ":suffix" => $suffix,
                ":contact" => $contact,
                ":email" => $email,
                ":gender" => $gender,
                ":password" => $password,
                ":civilstatus" => $civilstatus,
                ":education" => $education,
                ":soloparent" => $soloparent,
                ":children" => $children,
                ":member" => $member,
                ":program" => $program,
                ":dependent" => $dependent,
                ":relationship" => $relationship,
                ":housenumber" => $housenumber,
                ":street" => $street,
                ":barangay" => $barangay,
                ":city" => $city,
                ":province" => $province,
                ":zipcode" => $zipcode,
                ":birthplace" => $birthplace,
                ":occupation" => $occupation,
                ":school" => $school,
                ":yearlevel" => $yearlevel,
                ":course" => $course,
                ":benefits" => $benefits,
                ":enrolled" => $enrolled,
                ":attained" => $attained,
                ":senior" => $senior,
                ":issues" => $issues,
                ":documents" => $documents,
                ":status" => "Pending",
            ]);

            return $this->pdo->lastInsertId();
        }

        public function fetch_data(){
            $fetch = "SELECT * FROM application";
            $prep = $this->pdo->prepare($fetch);
            $prep->execute();

            $rows = $prep->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$row) {
                $row['benefits'] = $this->format_selections($row['benefits'] ?? null);
                $row['senior'] = $this->format_selections($row['senior'] ?? null);
            }

            return $rows;
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

       public function fetch_resident_applications($applicant_id){
            $fetch = "SELECT * FROM application WHERE applicant_id = :applicant_id";

            $prep = $this->pdo->prepare($fetch);
            $prep->execute([
                ":applicant_id" => $applicant_id,
            ]);

            $rows = $prep->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$row) {
                $row['benefits'] = $this->format_selections($row['benefits'] ?? null);
                $row['senior'] = $this->format_selections($row['senior'] ?? null);
            }

            return $rows;
        }

        public function fetch_profile_data($user_id){
            $fetch = "SELECT
                        ri.firstname,
                        ri.middlename,
                        ri.lastname,
                        ri.suffix,
                        ri.contact,
                        u.email,
                        ri.housenumber,
                        ri.street,
                        ri.barangay,
                        ri.city,
                        ri.province,
                        ri.zipcode

                      FROM resident_information ri
                      INNER JOIN user u ON ri.user_id = u.user_id
                      WHERE ri.user_id = ?";

            $prep = $this->pdo->prepare($fetch);
            $prep->execute([$user_id]);

            return $prep->fetch();
        }

        public function applicant_login($email, $password){
            $email = trim(strtolower($email));

            $fetch = "SELECT * FROM application WHERE LOWER(email) = :email";
            $prep = $this->pdo->prepare($fetch);
            $prep->execute([
                ":email" => $email
            ]);

            $row = $prep->fetch();

            if($row && password_verify($password, $row['password'])){
                return $row;
            }

            return false;
        }
    }
?>
