<?php
declare(strict_types=1);
session_start();

define('BASE_PATH', dirname(__DIR__));              // C:\xampp\htdocs\autonexus
define('APP_ROOT', BASE_PATH . '/app');             // C:\xampp\htdocs\autonexus\app
define('CONFIG_PATH', BASE_PATH . '/config');       // C:\xampp\htdocs\autonexus\config
   // optional



/*
|--------------------------------------------------------------------------|
| Bootstrap (config + DB helper)
|--------------------------------------------------------------------------|
*/
require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';


/*
|--------------------------------------------------------------------------|
| Autoload app\ namespace
|--------------------------------------------------------------------------|
*/
spl_autoload_register(function ($class) {
    $prefix = 'app\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) require $path;
});

use app\core\Router;

$router = new Router($config ?? []);

/** ======================
 *  ADMIN DASHBOARD
 *  ====================== */
$router->get('/',        [\app\controllers\Admin\HomeController::class, 'index']);  // root -> admin home
$router->get('/admin',   [\app\controllers\Admin\HomeController::class, 'index']);  // explicit /admin

/** ======================
 *  ADMIN: Branches
 *  ====================== */
$router->get('/admin/branches',                 [\app\controllers\Admin\BranchesController::class, 'index']);
$router->get('/admin/branches/create',          [\app\controllers\Admin\BranchesController::class, 'create']);
$router->post('/admin/branches',                [\app\controllers\Admin\BranchesController::class, 'store']);
$router->get('/admin/branches/{code}',          [\app\controllers\Admin\BranchesController::class, 'show']);
$router->get('/admin/branches/{code}/edit',     [\app\controllers\Admin\BranchesController::class, 'edit']);
$router->post('/admin/branches/{code}',         [\app\controllers\Admin\BranchesController::class, 'update']);
$router->post('/admin/branches/{code}/delete',  [\app\controllers\Admin\BranchesController::class, 'destroy']);

/** ======================
 *  ADMIN: Service Managers
 *  ====================== */
$router->get('/admin/service-managers',                 [\app\controllers\Admin\ServiceManagersController::class, 'index']);
$router->get('/admin/service-managers/list',            [\app\controllers\Admin\ServiceManagersController::class, 'list']);
$router->get('/admin/service-managers/create',          [\app\controllers\Admin\ServiceManagersController::class, 'create']);
$router->post('/admin/service-managers',                [\app\controllers\Admin\ServiceManagersController::class, 'store']);
$router->get('/admin/service-managers/{id}',            [\app\controllers\Admin\ServiceManagersController::class, 'show']);
$router->get('/admin/service-managers/{id}/edit',       [\app\controllers\Admin\ServiceManagersController::class, 'edit']);
$router->post('/admin/service-managers/{id}',           [\app\controllers\Admin\ServiceManagersController::class, 'update']);
$router->post('/admin/service-managers/{id}/delete',    [\app\controllers\Admin\ServiceManagersController::class, 'destroy']);

/** ======================
 *  ADMIN: Customers
 *  ====================== */
$router->get('/admin/customers',                 [\app\controllers\Admin\CustomersController::class, 'index']);
$router->get('/admin/customers/create',          [\app\controllers\Admin\CustomersController::class, 'create']);
$router->post('/admin/customers',                [\app\controllers\Admin\CustomersController::class, 'store']);
$router->get('/admin/customers/{id}',            [\app\controllers\Admin\CustomersController::class, 'show']);
$router->get('/admin/customers/{id}/edit',       [\app\controllers\Admin\CustomersController::class, 'edit']);
$router->post('/admin/customers/{id}',           [\app\controllers\Admin\CustomersController::class, 'update']);
$router->post('/admin/customers/{id}/delete',    [\app\controllers\Admin\CustomersController::class, 'destroy']);

// ADMIN: Dashboard
use app\controllers\admin\DashboardController;
$router->get('/admin-dashboard', [DashboardController::class, 'index']);

/** ======================
 *  ADMIN: Mechanics
 *  ====================== */


use app\controllers\admin\MechanicsController;

// List page
$router->get('/admin/mechanics', [MechanicsController::class, 'index']);  // <-- add this

// Already present
$router->get('/admin/mechanics/create', [MechanicsController::class, 'create']);
$router->post('/admin/mechanics',       [MechanicsController::class, 'store']);
$router->get('/admin/mechanics/{id}',        [MechanicsController::class, 'show']);
$router->get('/admin/mechanics/{id}/edit',   [MechanicsController::class, 'edit']);
$router->post('/admin/mechanics/{id}',       [MechanicsController::class, 'update']);
$router->post('/admin/mechanics/{id}/delete',[MechanicsController::class, 'destroy']);

/** ======================
 *  ADMIN: Supervisors (route by code)
 *  ====================== */
use app\controllers\admin\SupervisorsController;

$router->get ('/admin/supervisors',               [SupervisorsController::class, 'index']);
$router->get ('/admin/supervisors/create',        [SupervisorsController::class, 'create']);
$router->post('/admin/supervisors',               [SupervisorsController::class, 'store']);
$router->get ('/admin/supervisors/{code}',        [SupervisorsController::class, 'show']);
$router->get ('/admin/supervisors/{code}/edit',   [SupervisorsController::class, 'edit']);
$router->post('/admin/supervisors/{code}',        [SupervisorsController::class, 'update']);
$router->post('/admin/supervisors/{code}/delete', [SupervisorsController::class, 'destroy']);


/** ======================
 *  ADMIN: Appointments (route by code)
 *  ====================== */
// public/index.php or wherever you register routes
use app\controllers\admin\AppointmentsController;

$router->get('/admin/admin-appointments', [AppointmentsController::class, 'index']);

use app\controllers\admin\ServicesController;

/** ======================
 *  ADMIN: View Services (route by code)
 *  ====================== */
$router->get('/admin/admin-viewservices', [ServicesController::class, 'index']);

use app\controllers\admin\PricingController;

$router->get('/admin/admin-updateserviceprice', [PricingController::class, 'index']);

use app\controllers\admin\ApprovalController;

// ...
$router->get('/admin/admin-serviceapproval', [ApprovalController::class, 'index']);

use app\controllers\admin\OngoingServicesController;

$router->get('/admin/admin-ongoingservices', [OngoingServicesController::class, 'index']);

use app\controllers\admin\ServiceHistoryController;

$router->get('/admin/admin-servicehistory', [ServiceHistoryController::class, 'index']);

use app\controllers\admin\FeedbackController;

$router->get('/admin/admin-viewfeedback', [FeedbackController::class, 'index']);

use app\controllers\admin\NotificationsController;

$router->get('/admin/admin-notifications', [NotificationsController::class, 'index']);

use app\controllers\admin\InvoicesController;

$router->get('/admin/admin-viewinvoices', [InvoicesController::class, 'index']);

use app\controllers\admin\ReportsController;

$router->get('/admin/admin-viewreports', [ReportsController::class, 'index']);

use app\controllers\admin\ReceptionistsController;

$router->get('/admin/viewreceptionist', [ReceptionistsController::class, 'index']);

/** Dev helper */
$router->get('/test-managers', function () {
    require_once __DIR__ . '/../app/model/admin/Manager.php';
    $m = new \app\model\Admin\Manager();
    header('Content-Type: text/plain');
    print_r($m->all());
});


// Show the login form (GET /login)
$router->get('/login', function () {
    // adjust the path to your actual login form file
    require __DIR__ . '/../app/views/login/login.php';
});

// Handle login submit (POST /login)
$router->post('/login', function () {
    // adjust path to where db_login.php actually lives
    require __DIR__ . '/../app/controllers/db_login.php';
});

// Registration form + submit
$router->get('/register', function () {
    require __DIR__ . '/../app/views/register/index.php'; // your form page
});
$router->post('/register', function () {
    require __DIR__ . '/../register.php'; // the handler above
});

/*
|--------------------------------------------------------------------------|
| CLEAN ROUTES to match your updated sidebar (Option A)
| Add placeholders so links don't 404; replace with real controllers later.
|--------------------------------------------------------------------------|
*/


$router->get('/mechanics',         fn() => print 'TODO: Mechanics page');
$router->get('/receptionists',     fn() => print 'TODO: Receptionists page');

$router->get('/services',          fn() => print 'TODO: Services page');
$router->get('/pricing',           fn() => print 'TODO: Pricing page');
$router->get('/service-approval',  fn() => print 'TODO: Service Approval page');
$router->get('/appointments',      fn() => print 'TODO: Appointments page');
$router->get('/service-progress',  fn() => print 'TODO: Service Progress page');
$router->get('/service-history',   fn() => print 'TODO: Service History page');
$router->get('/feedback',          fn() => print 'TODO: Feedback page');
$router->get('/notifications',     fn() => print 'TODO: Notifications page');
$router->get('/reports',           fn() => print 'TODO: Reports page');
$router->get('/invoices',          fn() => print 'TODO: Invoices page');

/*
|--------------------------------------------------------------------------|
| Normalize request path (works with or without /public in URL)
|--------------------------------------------------------------------------|
*/
$incomingPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$incomingPath = $incomingPath === '' ? '/' : $incomingPath;

// 1) Strip the path where index.php lives (e.g. /autonexus/public)
$scriptBase = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/'); // e.g. /autonexus/public
if ($scriptBase && strpos($incomingPath, $scriptBase) === 0) {
    $incomingPath = substr($incomingPath, strlen($scriptBase));
    if ($incomingPath === '' || $incomingPath === false) $incomingPath = '/';
}

// 2) Also strip BASE_URL if present (e.g. /autonexus)
$base = rtrim(BASE_URL ?? '', '/'); // e.g. /autonexus
if ($base && strpos($incomingPath, $base) === 0) {
    $incomingPath = substr($incomingPath, strlen($base));
    if ($incomingPath === '' || $incomingPath === false) $incomingPath = '/';
}

$incomingPath = rtrim($incomingPath, '/') ?: '/';

/*
|--------------------------------------------------------------------------|
| Debug line (optional) — comment out when done
|--------------------------------------------------------------------------|
*/
file_put_contents(
    __DIR__ . '/../debug.log',
    date('c')
    . " M=" . ($_SERVER['REQUEST_METHOD'] ?? 'GET')
    . " ORIG=" . ($_SERVER['REQUEST_URI'] ?? '')
    . " BASE=" . (defined('BASE_URL') ? BASE_URL : 'undef')
    . " SCRIPT_BASE=" . $scriptBase
    . " PATH=" . $incomingPath . "\n",
    FILE_APPEND
);

/*
|--------------------------------------------------------------------------|
| Dispatch (supports both Router::dispatch() signatures)
|--------------------------------------------------------------------------|
*/
try {
    $methodInfo = new \ReflectionMethod($router, 'dispatch');
    $argc = $methodInfo->getNumberOfParameters();

    if ($argc >= 2) {
        // Newer signature: dispatch($path, $method)
        $router->dispatch($incomingPath, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    } else {
        // Legacy signature: dispatch() reads $_SERVER['REQUEST_URI']
        $_SERVER['REQUEST_URI'] = $incomingPath;
        $router->dispatch();
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<pre style='font:14px/1.4 monospace'>Router dispatch error:\n\n"
       . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
       . "\n\n" . $e->getTraceAsString()
       . "</pre>";
}
