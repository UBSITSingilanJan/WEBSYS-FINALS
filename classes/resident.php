<?php
class Resident {

    private PDO $pdo;

    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function insert_data(
        $firstname,
        $middlename,
        $lastname,
        $age,
        $gender,
        $contact,
        $address,
        $user_id
    ) {
        $insert = "INSERT INTO resident_information
            (firstname, middlename, lastname, age, gender, contact, address, user_id)
            VALUES
            (:firstname, :middlename, :lastname, :age, :gender, :contact, :address, :user_id)
            ON DUPLICATE KEY UPDATE
                firstname = VALUES(firstname),
                middlename = VALUES(middlename),
                lastname = VALUES(lastname),
                age = VALUES(age),
                gender = VALUES(gender),
                contact = VALUES(contact),
                address = VALUES(address)";

        $prep = $this->pdo->prepare($insert);

        $prep->execute([
            ":firstname" => $firstname,
            ":middlename" => $middlename,
            ":lastname" => $lastname,
            ":age" => $age,
            ":gender" => $gender,
            ":contact" => $contact,
            ":address" => $address,
            ":user_id" => $user_id
        ]);
    }

    public function fetch_data() {
        $fetch = "SELECT * FROM resident_information";

        $prep = $this->pdo->prepare($fetch);
        $prep->execute();

        return $prep->fetchAll();
    }

    public function fetch_resident($user_id) {
        $fetch = "SELECT * FROM resident_information
                  WHERE user_id = :user_id";

        $prep = $this->pdo->prepare($fetch);
        $prep->execute([
            ":user_id" => $user_id
        ]);

        return $prep->fetch();
    }

	public function count_residents(){
    $count = "SELECT COUNT(DISTINCT
                CONCAT(
                    LOWER(TRIM(firstname)), '|',
                    LOWER(TRIM(COALESCE(middlename, ''))), '|',
                    LOWER(TRIM(lastname))
                )
            ) AS total
            FROM resident_information";

    $prep = $this -> pdo -> prepare($count);
    $prep -> execute();

    return $prep -> fetch();
}
}
?>