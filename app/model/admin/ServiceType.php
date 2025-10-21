<?php
namespace app\model\admin;

use PDO;

class ServiceType
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function all(): array
    {
        $sql = "SELECT type_id, type_name FROM service_types ORDER BY type_name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
