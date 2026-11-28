
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

        public function insert_data($firstname, $middlename, $lastname, $suffix, $contact, $email, $gender, $civilstatus, $education, $soloparent, $children, $member, $program, $dependent, $relationship, $housenumber, $street, $barangay, $city, $province, $zipcode, $birthplace, $occupation, $school, $yearlevel, $course, $benefits, $enrolled, $attained, $senior, $issues, $aid_type, $resident_id, $user_id, $documents){
            $ownsTransaction = !$this->pdo->inTransaction();
            if ($ownsTransaction) {
                $this->pdo->beginTransaction();
            }

            try {
                $insert = "INSERT INTO application(
                    firstname, middlename, lastname, suffix, contact, email,
                    gender, civilstatus, education, soloparent, children,
                    member, program, dependent, relationship, housenumber,
                    street, barangay, city, province, zipcode, birthplace,
                    occupation, school, yearlevel, course, benefits, enrolled,
                    attained, senior, issues, aid_type, resident_id, user_id, status
                )
                VALUES(
                    :firstname, :middlename, :lastname, :suffix, :contact, :email,
                    :gender, :civilstatus, :education, :soloparent, :children,
                    :member, :program, :dependent, :relationship, :housenumber,
                    :street, :barangay, :city, :province, :zipcode, :birthplace,
                    :occupation, :school, :yearlevel, :course, :benefits, :enrolled,
                    :attained, :senior, :issues, :aid_type, :resident_id, :user_id, :status
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
                    ":resident_id" => $resident_id,
                    ":user_id" => $user_id,
                    ":status" => "Pending",
                ]);

                $applicantId = $this->pdo->lastInsertId();
                $documentInsert = $this->pdo->prepare(
                    "INSERT INTO application_document
                     (applicant_id, requirement_label, original_name, mime_type, file_data)
                     VALUES (:applicant_id, :requirement_label, :original_name, :mime_type, :file_data)"
                );

                foreach ($documents as $document) {
                    $documentInsert->bindValue(':applicant_id', $applicantId, PDO::PARAM_INT);
                    $documentInsert->bindValue(':requirement_label', $document['requirement_label'] ?? 'Supporting Document');
                    $documentInsert->bindValue(':original_name', $document['original_name']);
                    $documentInsert->bindValue(':mime_type', $document['mime_type']);
                    $documentInsert->bindValue(':file_data', $document['file_data'], PDO::PARAM_LOB);
                    $documentInsert->execute();
                }

                if ($ownsTransaction) {
                    $this->pdo->commit();
                }
                return $applicantId;
            } catch (PDOException $exception) {
                if ($ownsTransaction && $this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $exception;
            }
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

        public function fetch_resident_applications($user_id){
            $fetch = "SELECT * FROM application WHERE user_id = :user_id";

            $prep = $this->pdo->prepare($fetch);
            $prep->execute([
                ":user_id" => $user_id,
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
                        ri.*,
                        u.email AS account_email
                      FROM resident_information ri
                      INNER JOIN user u ON ri.user_id = u.user_id
                      WHERE ri.user_id = ?";

            $prep = $this->pdo->prepare($fetch);
            $prep->execute([$user_id]);

            return $prep->fetch();
        }
    }
?>
