<?php
namespace app\model\supervisor;

use PDO;

class Checklist
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * Get checklist items for a work order
     */
    public function getByWorkOrder(int $work_order_id): array
    {
        $sql = "
            SELECT id, item_name, status
            FROM checklist
            WHERE work_order_id = ?
            ORDER BY id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$work_order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add checklist item
     */
    public function addItem(int $work_order_id, string $item_name): void
    {
        $sql = "
            INSERT INTO checklist (work_order_id, item_name, status)
            VALUES (?, ?, 'pending')
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$work_order_id, $item_name]);
    }

    /**
     * Update checklist item status
     */
    public function updateStatus(int $id, string $status): void
    {
        $sql = "
            UPDATE checklist
            SET status = ?
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status, $id]);
    }

    public function createFromServiceTemplateArray(int $serviceId): array
{
    $sql = "SELECT step_name FROM service_checklist_templates WHERE service_id = ? ORDER BY step_order ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$serviceId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /**
 * Create checklist item (used during work order creation)
 */
public function create(array $data): void
{
    $sql = "
        INSERT INTO checklist (work_order_id, item_name, status)
        VALUES (:work_order_id, :item_name, :status)
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'work_order_id' => $data['work_order_id'],
        'item_name'     => $data['item_name'],
        'status'        => $data['status'] ?? 'pending',
    ]);
}

public function getTemplateByService(int $serviceId): array
{
    $sql = "SELECT step_name FROM service_checklist_templates WHERE service_id = ? ORDER BY step_order ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$serviceId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function deleteByWorkOrder(int $workOrderId): void
{
    $sql = "DELETE FROM checklist WHERE work_order_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$workOrderId]);
}

public function getByWorkOrderId(int $workOrderId): array
{
    $stmt = $this->pdo->prepare(
        "SELECT id, item_name FROM checklist WHERE work_order_id = ? ORDER BY id ASC"
    );
    $stmt->execute([$workOrderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function deleteById(int $id): void
{
    $stmt = $this->pdo->prepare("DELETE FROM checklist WHERE id = ?");
    $stmt->execute([$id]);
}



}

