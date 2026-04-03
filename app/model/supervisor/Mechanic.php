<?php
namespace app\model\supervisor;
use PDO;

class Mechanic {
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // existing helper
    }
    public function getAllMechanics() {
        $stmt = $this->pdo->query("SELECT * FROM mechanics");
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
