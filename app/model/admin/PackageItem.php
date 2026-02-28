<?php
namespace app\model\admin;

use PDO;

class PackageItem
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

    public function replaceItems(int $packageServiceId, array $childServiceIds): void
    {
        // remove existing
        $del = $this->pdo->prepare("DELETE FROM service_package_items WHERE package_service_id = :pid");
        $del->execute(['pid' => $packageServiceId]);

        if (empty($childServiceIds)) return;

        $ins = $this->pdo->prepare(
            "INSERT INTO service_package_items (package_service_id, child_service_id, quantity)
             VALUES (:pid, :cid, 1)"
        );
        foreach ($childServiceIds as $cid) {
            $ins->execute(['pid' => $packageServiceId, 'cid' => (int)$cid]);
        }
    }

    public function childIds(int $packageServiceId): array {
  $st = $this->pdo->prepare("SELECT child_service_id FROM service_package_items WHERE package_service_id = :pid");
  $st->execute(['pid' => $packageServiceId]);
  return array_map('intval', $st->fetchAll(\PDO::FETCH_COLUMN, 0));
}

}
