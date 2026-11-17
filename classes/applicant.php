
<?php
    class Applicant{
        private PDO $pdo;

        function __construct(PDO $pdo){
            $this -> pdo = $pdo;
        }

        public function insert_data($firstname, $middlename, $lastname, $suffix, $contact, $email, $gender, $civilstatus, $education, $soloparent, $children, $member, $program, $dependent, $relationship, $housenumber, $street, $barangay, $city, $province, $zipcode, $birthplace, $occupation, $school, $yearlevel, $course, $benefits, $enrolled, $attained, $senior, $issues, $aid_type, $user_id){
            $insert = "INSERT INTO application(firstname, middlename, lastname, suffix, contact, email, gender, civilstatus, education, soloparent, children, member, program, dependent, relationship, housenumber, street, barangay, city, province, zipcode, birthplace, occupation, school, yearlevel, course, benefits, enrolled, attained, senior, issues, aid_type, user_id, status)
                       VALUES(:firstname, :middlename, :lastname, :suffix, :contact, :email, :gender, :civilstatus, :education, :soloparent, :children, :member, :program, :dependent, :relationship, :housenumber, :street, :barangay, :city, :province, :zipcode, :birthplace, :occupation, :school, :yearlevel, :course, :benefits, :enrolled, :attained, :senior, :issues, :aid_type, :user_id, :status)";

            $prep = $this->pdo->prepare($insert);
            $prep->execute([
                ":firstname" => $firstname,
                ":middlename" => $middlename,
                ":lastname" => $lastname,
                ":suffix" => $suffix,
                ":contact" => $contact,
                ":email" => $email,
                ":gender" => $gender,
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
                ":aid_type" => $aid_type,
                ":user_id" => $user_id,
                ":status" => "Pending",
            ]);

            return $this->pdo->lastInsertId();
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
            $fetch = "SELECT * FROM application WHERE user_id = :user_id";

            $prep = $this -> pdo -> prepare($fetch);
            $prep -> execute([
                ":user_id" => $user_id,
            ]);

            return $prep -> fetchAll();
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
    }
?>
