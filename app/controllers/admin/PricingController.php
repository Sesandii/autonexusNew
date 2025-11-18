<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Service;

class PricingController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    /** GET/POST /admin/pricing */
    public function index(): void
    {
        // Handle a single-row save
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $id    = (int)($_POST['service_id'] ?? 0);
            $price = trim((string)($_POST['price'] ?? ''));

            $ok = false; $msg = 'Invalid form submission.';
            if ($id > 0 && $price !== '' && is_numeric($price) && (float)$price >= 0) {
                try {
                    $svc = new Service();
                    // Update only the price column
                    $svc->updateById($id, ['default_price' => (float)$price]);
                    $ok  = true;
                    $msg = 'Price updated.';
                } catch (\Throwable $e) {
                    $ok  = false;
                    $msg = 'Could not update price. Please try again.';
                }
            } else {
                $msg = 'Please enter a valid non-negative number for price.';
            }

            $_SESSION['flash'] = $msg;

            // PRG pattern → reload page so the table shows fresh data
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/pricing');
            exit;
        }

        // Load services for the table (you can filter to only packages if you want)
        $svc = new Service();
        $services = $svc->allWithTypeAndBranches(); // contains type_name + default_price

        $this->view('admin/admin-updateserviceprice/index', [
            'pageTitle' => 'Service Pricing Management',
            'current'   => 'pricing',
            'services'  => $services,
            'flash'     => $_SESSION['flash'] ?? null,
        ]);

        unset($_SESSION['flash']);
    }

    /** Guard: must be logged in and have admin role */
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
