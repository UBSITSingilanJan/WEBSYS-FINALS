
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

    public function link_application_to_resident($applicant_id, $resident_id) {
        $check = $this->pdo->prepare(
            "SELECT a.user_id, a.resident_id, ri.resident_id AS matched_resident_id,
                    ri.user_id AS resident_user_id
             FROM application a
             LEFT JOIN resident_information ri ON ri.resident_id = :resident_id
             WHERE a.applicant_id = :applicant_id"
        );
        $check->execute([
            ':resident_id' => $resident_id,
            ':applicant_id' => $applicant_id,
        ]);
        $application = $check->fetch(PDO::FETCH_ASSOC);

        if (
            !$application ||
            $application['matched_resident_id'] === null ||
            $application['resident_id'] !== null ||
            (
                $application['user_id'] !== null &&
                (
                    $application['resident_user_id'] === null ||
                    (int)$application['user_id'] !== (int)$application['resident_user_id']
                )
            )
        ) {
            return false;
        }

        $update = $this->pdo->prepare(
            "UPDATE application
             SET resident_id = :resident_id
             WHERE applicant_id = :applicant_id AND resident_id IS NULL"
        );
        $update->execute([
            ':resident_id' => $resident_id,
            ':applicant_id' => $applicant_id,
        ]);

        return $update->rowCount() === 1;
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
                    a.resident_id,
                    a.firstname,
                    a.middlename,
                    a.lastname,
                    a.email,
                    a.application_date,
                    a.aid_type,
                    a.status,

                    (
                        SELECT COUNT(*)
                        FROM application previous_application
                        WHERE previous_application.applicant_id <> a.applicant_id
                        AND (
                            (a.user_id IS NOT NULL AND previous_application.user_id = a.user_id)
                            OR (a.resident_id IS NOT NULL AND previous_application.resident_id = a.resident_id)
                            OR (
                                a.email IS NOT NULL AND a.email <> ''
                                AND previous_application.email = a.email
                            )
                        )
                    ) AS previous_request_count,

                    (
                        SELECT GROUP_CONCAT(
                            CONCAT(previous_application.aid_type, '::', previous_application.status)
                            ORDER BY previous_application.applicant_id DESC
                            SEPARATOR '||'
                        )
                        FROM application previous_application
                        WHERE previous_application.applicant_id <> a.applicant_id
                        AND (
                            (a.user_id IS NOT NULL AND previous_application.user_id = a.user_id)
                            OR (a.resident_id IS NOT NULL AND previous_application.resident_id = a.resident_id)
                            OR (
                                a.email IS NOT NULL AND a.email <> ''
                                AND previous_application.email = a.email
                            )
                        )
                    ) AS previous_requests,

                    (
                        SELECT COUNT(*)
                        FROM application previous_application
                        WHERE previous_application.applicant_id <> a.applicant_id
                        AND previous_application.status IN ('Pending', 'Approved')
                        AND (
                            (a.user_id IS NOT NULL AND previous_application.user_id = a.user_id)
                            OR (a.resident_id IS NOT NULL AND previous_application.resident_id = a.resident_id)
                            OR (
                                a.email IS NOT NULL AND a.email <> ''
                                AND previous_application.email = a.email
                            )
                        )
                    ) AS other_active_aid_count,

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

    private function is_allowed_document_mime($mimeType) {
        return in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
        ], true);
    }

    public function fetch_application_documents($applicant_ids) {

        $applicant_ids = array_values(array_unique(array_filter(
            array_map('intval', $applicant_ids),
            function ($applicant_id) {
                return $applicant_id > 0;
            }
        )));

        if (empty($applicant_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($applicant_ids), '?'));
        $documentsByApplicant = [];

        $sql = "SELECT document_id, applicant_id, original_name
                FROM application_document
                WHERE applicant_id IN ($placeholders)
                ORDER BY document_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($applicant_ids);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $document) {
            $applicantId = (int)$document['applicant_id'];
            $documentsByApplicant[$applicantId][] = [
                'document_id' => (int)$document['document_id'],
                'legacy_index' => null,
                'filename' => $document['original_name'],
            ];
        }

        $sql = "SELECT applicant_id, documents
                FROM application
                WHERE applicant_id IN ($placeholders)
                AND documents IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($applicant_ids);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $application) {
            $legacyDocuments = json_decode($application['documents'], true);
            if (!is_array($legacyDocuments)) {
                continue;
            }

            $applicantId = (int)$application['applicant_id'];
            foreach ($legacyDocuments as $index => $path) {
                if (!is_int($index) || !is_string($path)) {
                    continue;
                }

                $documentsByApplicant[$applicantId][] = [
                    'document_id' => null,
                    'legacy_index' => $index,
                    'filename' => basename(str_replace('\\', '/', $path)),
                ];
            }
        }

        return $documentsByApplicant;
    }

    public function fetch_application_document($applicant_id, $document_id) {

        $sql = "SELECT original_name, mime_type, file_data
                FROM application_document
                WHERE applicant_id = :applicant_id
                AND document_id = :document_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':applicant_id' => $applicant_id,
            ':document_id' => $document_id,
        ]);

        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$document || !$this->is_allowed_document_mime($document['mime_type'])) {
            return false;
        }

        $fileData = $document['file_data'];
        if (is_resource($fileData)) {
            $fileData = stream_get_contents($fileData);
        }
        if (!is_string($fileData)) {
            return false;
        }

        return [
            'filename' => $document['original_name'],
            'mime_type' => $document['mime_type'],
            'file_data' => $fileData,
        ];
    }

    public function fetch_legacy_application_document($applicant_id, $document_index) {

        $sql = "SELECT documents
                FROM application
                WHERE applicant_id = :applicant_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':applicant_id' => $applicant_id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            return false;
        }

        $documents = json_decode($application['documents'] ?? '', true);
        if (!is_array($documents) || !isset($documents[$document_index])) {
            return false;
        }

        $documentPath = str_replace('\\', '/', (string)$documents[$document_index]);
        $prefix = 'uploads/applications/';
        if (strpos($documentPath, $prefix) !== 0) {
            return false;
        }

        $filename = substr($documentPath, strlen($prefix));
        if (
            basename($filename) !== $filename ||
            !preg_match('/\Aapplication_[a-f0-9.]+\.(jpg|png|webp|pdf)\z/i', $filename)
        ) {
            return false;
        }

        $uploadDirectory = realpath(__DIR__ . '/../uploads/applications');
        if ($uploadDirectory === false) {
            return false;
        }

        $resolvedPath = realpath($uploadDirectory . DIRECTORY_SEPARATOR . $filename);
        $allowedPrefix = $uploadDirectory . DIRECTORY_SEPARATOR;
        if (
            $resolvedPath === false ||
            strncmp($resolvedPath, $allowedPrefix, strlen($allowedPrefix)) !== 0 ||
            !is_file($resolvedPath) ||
            !is_readable($resolvedPath)
        ) {
            return false;
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($resolvedPath);
        if (!$this->is_allowed_document_mime($mimeType)) {
            return false;
        }

        $fileData = file_get_contents($resolvedPath);
        if ($fileData === false) {
            return false;
        }

        return [
            'filename' => $filename,
            'mime_type' => $mimeType,
            'file_data' => $fileData,
        ];
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
                WHERE a.status <> 'Rejected'
                ORDER BY r.release_id DESC
                LIMIT 10";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch_all_releases() {
        $sql = "SELECT
                    r.release_id,
                    r.applicant_id,
                    r.amount,
                    r.release_date,
                    r.remarks,
                    a.resident_id,
                    a.aid_type,
                    COALESCE(
                        NULLIF(TRIM(CONCAT_WS(' ', ri.firstname, ri.middlename, ri.lastname)), ''),
                        NULLIF(TRIM(CONCAT_WS(' ', a.firstname, a.middlename, a.lastname)), '')
                    ) AS resident_name
                FROM assistance_release r
                INNER JOIN application a ON a.applicant_id = r.applicant_id
                LEFT JOIN resident_information ri ON ri.resident_id = a.resident_id
                ORDER BY r.release_date DESC, r.release_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function total_released_amount() {

        $sql = "SELECT COALESCE(SUM(amount), 0)
                FROM assistance_release";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
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
