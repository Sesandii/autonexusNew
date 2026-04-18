<?php
// app/model/public/ServicePublic.php
declare(strict_types=1);

namespace app\model\public;

use PDO;

class ServicePublic
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

    /**
     * Returns all ACTIVE services offered by a branch (by branch_code).
     * Uses branch_services (not branch_service).
     */
    public function byBranchCode(string $branchCode): array
    {
        $sql = "
            SELECT
                s.service_id,
                s.service_code,
                s.name           AS service_name,
                s.description,
                s.base_duration_minutes,
                s.default_price,
                COALESCE(st.type_name, 'Other') AS type_name
            FROM branches b
            JOIN branch_services bs  ON bs.branch_id = b.branch_id
            JOIN services s          ON s.service_id = bs.service_id
            LEFT JOIN service_types st ON st.type_id = s.type_id
            WHERE b.branch_code = :code
              AND COALESCE(b.status,'active') = 'active'
              AND COALESCE(s.status,'active') = 'active'
            ORDER BY st.type_name, s.name
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $branchCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
