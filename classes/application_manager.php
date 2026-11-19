
<?php

class ApplicationManager {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function validate_release_input($amount, $release_date) {

        if (
            !is_numeric($amount) ||
            (float)$amount <= 0 ||
            (float)$amount > 99999999.99
        ) {
            return [
                "success" => false,
                "message" => "Enter a valid assistance amount."
            ];
        }

        $dateObject = DateTime::createFromFormat('!Y-m-d', $release_date);

        if (
            !$dateObject ||
            $dateObject->format('Y-m-d') !== $release_date
        ) {
            return [
                "success" => false,
                "message" => "Enter a valid release date."
            ];
        }

        return ["success" => true];
    }

    private function check_approved_application($applicant_id) {

        $sql = "SELECT status
                FROM application
                WHERE applicant_id = :applicant_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":applicant_id" => $applicant_id
        ]);

        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            return [
                "success" => false,
                "message" => "Application record not found."
            ];
        }

        if ($application['status'] !== "Approved") {
            return [
                "success" => false,
                "message" => "Only approved applications can receive assistance releases."
            ];
        }

        return ["success" => true];
    }

    public function update_status($applicant_id, $status) {

        if (!in_array($status, ["Pending", "Approved", "Rejected"], true)) {
            return false;
        }

        $sql = "UPDATE application
                SET status = :status
                WHERE applicant_id = :applicant_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ":status" => $status,
            ":applicant_id" => $applicant_id
        ]);
    }

    /*
     * CREATE FIRST RELEASE
     */
    public function record_release($applicant_id, $amount, $release_date, $remarks) {

        $validation = $this->validate_release_input($amount, $release_date);

        if (!$validation['success']) {
            return $validation;
        }

        $applicationCheck = $this->check_approved_application($applicant_id);

        if (!$applicationCheck['success']) {
            return $applicationCheck;
        }

        $insert = "INSERT INTO assistance_release
                   (applicant_id, amount, release_date, remarks)
                   VALUES
                   (:applicant_id, :amount, :release_date, :remarks)";

        $stmt = $this->pdo->prepare($insert);

        $stmt->execute([
            ":applicant_id" => $applicant_id,
            ":amount" => number_format((float)$amount, 2, '.', ''),
            ":release_date" => $release_date,
            ":remarks" => trim($remarks) !== "" ? trim($remarks) : null
        ]);

        return [
            "success" => true,
            "message" => "Financial assistance release recorded successfully."
        ];
    }

    /*
     * UPDATE AN EXISTING RELEASE
     */
    public function update_release($release_id, $applicant_id, $amount, $release_date, $remarks) {

        $validation = $this->validate_release_input($amount, $release_date);

        if (!$validation['success']) {
            return $validation;
        }

        $applicationCheck = $this->check_approved_application($applicant_id);

        if (!$applicationCheck['success']) {
            return $applicationCheck;
        }

        // Make sure this release belongs to the selected applicant.
        $check = "SELECT release_id
                  FROM assistance_release
                  WHERE release_id = :release_id
                  AND applicant_id = :applicant_id";

        $stmt = $this->pdo->prepare($check);

        $stmt->execute([
            ":release_id" => $release_id,
            ":applicant_id" => $applicant_id
        ]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return [
                "success" => false,
                "message" => "Release record not found."
            ];
        }

        $update = "UPDATE assistance_release
                   SET amount = :amount,
                       release_date = :release_date,
                       remarks = :remarks
                   WHERE release_id = :release_id
                   AND applicant_id = :applicant_id";

        $stmt = $this->pdo->prepare($update);

        $stmt->execute([
            ":amount" => number_format((float)$amount, 2, '.', ''),
            ":release_date" => $release_date,
            ":remarks" => trim($remarks) !== "" ? trim($remarks) : null,
            ":release_id" => $release_id,
            ":applicant_id" => $applicant_id
        ]);

        return [
            "success" => true,
            "message" => "Assistance release updated successfully."
        ];
    }

    /*
     * FETCH APPLICATIONS AND THEIR LATEST RELEASE
     */
    public function fetch_applications($search = "") {

        $sql = "SELECT
                    a.applicant_id,
                    a.firstname,
                    a.middlename,
                    a.lastname,
                    a.email,
                    a.aid_type,
                    a.status,

                    COALESCE(
                        (
                            SELECT SUM(r.amount)
                            FROM assistance_release r
                            WHERE r.applicant_id = a.applicant_id
                        ),
                        0
                    ) AS total_released,

                    (
                        SELECT r.release_id
                        FROM assistance_release r
                        WHERE r.applicant_id = a.applicant_id
                        ORDER BY r.release_id DESC
                        LIMIT 1
                    ) AS latest_release_id,

                    (
                        SELECT r.amount
                        FROM assistance_release r
                        WHERE r.applicant_id = a.applicant_id
                        ORDER BY r.release_id DESC
                        LIMIT 1
                    ) AS latest_release_amount,

                    (
                        SELECT r.release_date
                        FROM assistance_release r
                        WHERE r.applicant_id = a.applicant_id
                        ORDER BY r.release_id DESC
                        LIMIT 1
                    ) AS latest_release_date,

                    (
                        SELECT r.remarks
                        FROM assistance_release r
                        WHERE r.applicant_id = a.applicant_id
                        ORDER BY r.release_id DESC
                        LIMIT 1
                    ) AS latest_release_remarks

                FROM application a";

        $params = [];

        if ($search !== "") {

            $sql .= " WHERE (
                        a.firstname LIKE :search1
                        OR a.middlename LIKE :search2
                        OR a.lastname LIKE :search3
                        OR a.email LIKE :search4
                        OR a.aid_type LIKE :search5
                    )";

            $keyword = "%" . $search . "%";

            $params = [
                ":search1" => $keyword,
                ":search2" => $keyword,
                ":search3" => $keyword,
                ":search4" => $keyword,
                ":search5" => $keyword
            ];
        }

        $sql .= " ORDER BY a.applicant_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * FETCH RECENT RELEASE HISTORY
     */
    public function fetch_recent_releases() {

        $sql = "SELECT
                    r.release_id,
                    r.amount,
                    r.release_date,
                    r.remarks,
                    a.firstname,
                    a.lastname,
                    a.aid_type
                FROM assistance_release r
                INNER JOIN application a
                    ON r.applicant_id = a.applicant_id
                ORDER BY r.release_id DESC
                LIMIT 10";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * COUNT APPLICATIONS
     */
    public function count_applications() {

        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END), 0) AS pending,
                    COALESCE(SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END), 0) AS approved,
                    COALESCE(SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END), 0) AS rejected
                FROM application";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
