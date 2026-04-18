<?php
namespace app\model\supervisor;

use PDO;

class ServicePhoto
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }


    public function create(int $workOrderId, string $fileName): void
{
    $stmt = $this->pdo->prepare(
        "INSERT INTO service_photos (work_order_id, file_name)
         VALUES (:wid, :file)"
    );

    $stmt->execute([
        'wid'  => $workOrderId,
        'file' => $fileName
    ]);
}

    public function getByWorkOrder(int $workOrderId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id,file_name 
             FROM service_photos 
             WHERE work_order_id = :id"
        );
        $stmt->execute(['id' => $workOrderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findById(int $id): ?array
{
    $stmt = $this->pdo->prepare(
        "SELECT * FROM service_photos WHERE id = :id"
    );
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

public function delete(int $id): void
{
    $stmt = $this->pdo->prepare(
        "DELETE FROM service_photos WHERE id = :id"
    );
    $stmt->execute(['id' => $id]);
}

}
