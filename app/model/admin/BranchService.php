<?php
namespace app\model\admin;

use PDO;

class BranchService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function attachToBranches(int $service_id, array $branch_ids): void
    {
        if (empty($branch_ids)) return;

        $sql  = "INSERT INTO branch_services (branch_id, service_id, created_at)
                 VALUES (:branch_id, :service_id, NOW())";
        $stmt = $this->pdo->prepare($sql);

        foreach ($branch_ids as $bid) {
            $stmt->execute([
                'branch_id'  => (int)$bid,
                'service_id' => $service_id,
            ]);
        }
    }

    public function branchIdsForService(int $service_id): array
{
    $stmt = $this->pdo->prepare("SELECT branch_id FROM branch_services WHERE service_id = :sid");
    $stmt->execute(['sid' => $service_id]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
}

public function replaceForService(int $service_id, array $branch_ids): void
{
    $del = $this->pdo->prepare("DELETE FROM branch_services WHERE service_id = :sid");
    $del->execute(['sid' => $service_id]);

    if (empty($branch_ids)) return;
    $ins = $this->pdo->prepare(
        "INSERT INTO branch_services (branch_id, service_id, created_at)
         VALUES (:bid, :sid, NOW())"
    );
    foreach ($branch_ids as $bid) {
        $ins->execute(['bid' => (int)$bid, 'sid' => $service_id]);
    }
}

}
