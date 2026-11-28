
<?php

class ProgramManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function add_program($program_name, $description, $created_by)
    {
        if ($this->program_exists($program_name)) {
            return false;
        }

        $sql = "INSERT INTO assistance_program
                (program_name, description, status, created_by)
                VALUES
                (:program_name, :description, 'Active', :created_by)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':program_name' => $program_name,
            ':description' => $description,
            ':created_by' => $created_by
        ]);
    }

    
    public function update_program($program_id, $program_name, $description)
    {
        $sql = "UPDATE assistance_program
                SET program_name = :program_name,
                    description = :description
                WHERE program_id = :program_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':program_name' => $program_name,
            ':description' => $description,
            ':program_id' => $program_id
        ]);
    }

    
    public function set_program_status($program_id, $status)
    {
        if (!in_array($status, ['Active', 'Inactive'])) {
            return false;
        }

        $sql = "UPDATE assistance_program
                SET status = :status
                WHERE program_id = :program_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':program_id' => $program_id
        ]);
    }

    
    public function fetch_programs($search = "")
    {
        $sql = "SELECT *
                FROM assistance_program";

        if (!empty($search)) {
            $sql .= " WHERE program_name LIKE :search
                      OR description LIKE :search";
        }

        $sql .= " ORDER BY program_id DESC";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function fetch_requirements_for_program($program_name)
    {
        $stmt = $this->pdo->prepare("SELECT description FROM assistance_program WHERE program_name = :name AND status = 'Active' LIMIT 1");
        $stmt->execute([':name' => $program_name]);
        $value = $stmt->fetchColumn();
        if ($value === false) {
            return [];
        }
        $lines = preg_split('/\r\n|\r|\n/', (string)$value);
        return array_values(array_filter(array_map('trim', $lines), function ($line) { return $line !== ''; }));
    }

    public function fetch_active_programs()
    {
        $sql = "SELECT program_id, program_name, description
                FROM assistance_program
                WHERE status = 'Active'
                ORDER BY program_name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function is_active_program($program_name)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM assistance_program
                WHERE program_name = :program_name
                AND status = 'Active'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':program_name' => $program_name
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] > 0;
    }

    
    private function program_exists($program_name)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM assistance_program
                WHERE program_name = :program_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':program_name' => $program_name
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] > 0;
    }

    
    public function count_programs($created_by)
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(CASE
                        WHEN status = 'Active' THEN 1
                        ELSE 0
                    END), 0) AS active,
                    COALESCE(SUM(CASE
                        WHEN status = 'Inactive' THEN 1
                        ELSE 0
                    END), 0) AS inactive
                FROM assistance_program
                WHERE created_by = :created_by";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':created_by' => $created_by
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
