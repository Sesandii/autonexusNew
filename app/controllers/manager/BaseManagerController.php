<?php

namespace app\controllers\Manager;

class BaseManagerController extends \app\core\Controller
{
    protected function getBranchId(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'manager') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        // If already valid
        if (!empty($_SESSION['user']['branch_id'])) {
            return (int)$_SESSION['user']['branch_id'];
        }

        // Always reload from DB (source of truth)
        $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $u['user_id']]);
        $branchId = $stmt->fetchColumn();

        if (!$branchId) {
            die("Manager branch not assigned in DB");
        }

        $_SESSION['user']['branch_id'] = (int)$branchId;

        return (int)$branchId;
    }
}