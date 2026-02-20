<?php
namespace app\model\supervisor;

use PDO;

class ServiceChecklistTemplate
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // global db() helper
    }

    /**
     * Get checklist steps for a service
     */
    public function getByService(int $service_id): array
    {
        $sql = "
            SELECT *
            FROM service_checklist_templates
            WHERE service_id = :service_id
            ORDER BY step_order ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'service_id' => $service_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add new checklist step
     */
    public function create(int $service_id, string $step_name, int $step_order = 1): void
    {
        $sql = "
            INSERT INTO service_checklist_templates
            (service_id, step_name, step_order)
            VALUES (:service_id, :step_name, :step_order)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'service_id' => $service_id,
            'step_name'  => $step_name,
            'step_order' => $step_order
        ]);
    }

    /**
     * Delete a checklist step
     */
    public function delete(int $template_id): void
    {
        $sql = "
            DELETE FROM service_checklist_templates
            WHERE template_id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $template_id
        ]);
    }
}
