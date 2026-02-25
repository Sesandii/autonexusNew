<?php

/*class Mechanic
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function allActive()
    {
        $stmt = $this->db->query("SELECT * FROM mechanics WHERE status = 'Available'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
*/
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

    public function updateStatus($mechanicId, $status)
{
    $stmt = $this->pdo->prepare("
        UPDATE mechanics
        SET status = :status
        WHERE mechanic_id = :id
    ");
    return $stmt->execute([
        ':status' => $status,
        ':id' => $mechanicId
    ]);
}

}
