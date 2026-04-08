<?php
namespace app\model\supervisor;
use PDO;

class Mechanic {
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // existing helper
    }

    public function getMechanicsByBranch(int $branchId) {
        $sql = "
            SELECT 
                m.*, 
                u.first_name, 
                u.last_name 
            FROM mechanics m
            INNER JOIN users u ON m.user_id = u.user_id
            WHERE m.branch_id = ?
            ORDER BY m.mechanic_code ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$branchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMechanics() {
        $sql = "
            SELECT 
                m.*, 
                u.first_name, 
                u.last_name 
            FROM mechanics m
            INNER JOIN users u ON m.user_id = u.user_id
            ORDER BY m.mechanic_code ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMechanicById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mechanics WHERE mechanic_id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($code, $status)
{
    $stmt = $this->pdo->prepare("
        UPDATE mechanics
        SET status = :status
        WHERE mechanic_code = :code
    ");
    return $stmt->execute([
        ':status' => $status,
        ':code' => $code
    ]);
}

}
