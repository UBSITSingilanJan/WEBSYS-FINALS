<?php
class Beneficiary {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function search_residents($search) {
        $prep = $this->pdo->prepare(
            "SELECT resident_id, firstname, middlename, lastname,
                    age, gender, contact, address
             FROM resident_information
             WHERE CONCAT_WS(' ', firstname, middlename, lastname) LIKE :name
                OR CAST(resident_id AS CHAR) LIKE :rid
                OR address LIKE :addr
                OR contact LIKE :contact
             ORDER BY lastname, firstname"
        );
        $prep->execute([
            ':name'    => "%{$search}%",
            ':rid'     => "%{$search}%",
            ':addr'    => "%{$search}%",
            ':contact' => "%{$search}%",
        ]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_resident($resident_id) {
        $prep = $this->pdo->prepare(
            "SELECT * FROM resident_information WHERE resident_id = :id LIMIT 1"
        );
        $prep->execute([':id' => $resident_id]);
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    public function aid_history($resident_id) {
        $prep = $this->pdo->prepare(
            "SELECT a.aid_type AS program, r.amount, r.release_date, r.remarks
             FROM assistance_release r
             INNER JOIN application a ON a.applicant_id = r.applicant_id
             WHERE a.resident_id = :id
             ORDER BY r.release_date DESC, r.release_id DESC"
        );
        $prep->execute([':id' => $resident_id]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function has_received($resident_id) {
        $prep = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM assistance_release r
             INNER JOIN application a ON a.applicant_id = r.applicant_id
             WHERE a.resident_id = :id"
        );
        $prep->execute([':id' => $resident_id]);
        return (int)$prep->fetchColumn() > 0;
    }

    public function approved_beneficiaries() {
        $prep = $this->pdo->prepare(
            "SELECT applicant_id, firstname, middlename, lastname, email, aid_type
             FROM application
             WHERE status = :status
             ORDER BY lastname, firstname"
        );
        $prep->execute([':status' => 'Approved']);
        $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
        return $fetch;
    }
}