<?php
declare(strict_types=1);
session_start();

define('BASE_PATH', dirname(__DIR__));              // C:\xampp\htdocs\autonexus
define('APP_ROOT', BASE_PATH . '/app');             // C:\xampp\htdocs\autonexus\app
define('CONFIG_PATH', BASE_PATH . '/config');       // C:\xampp\htdocs\autonexus\config
   // optional

//for multi lang support
// === i18n bootstrap ===
require_once BASE_PATH . '/app/core/I18n.php';

$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? ($_SESSION['lang'] ?? 'en'));
$_SESSION['lang'] = $lang;
setcookie('lang', $lang, time()+60*60*24*30, '/'); // 30 days site-wide

I18n::boot(BASE_PATH, $lang);
I18n::bufferStart();
// ======================

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
// use app\controllers\admin\DashboardController;
// $router->get('/admin-dashboard', [DashboardController::class, 'index']);

$router->get('/admin-dashboard',    [\app\controllers\Admin\DashboardController::class, 'index']);


use app\controllers\admin\AdminProfileController;

$router->get('/admin/profile',         [AdminProfileController::class, 'index']);
$router->post('/admin/profile/update', [AdminProfileController::class, 'update']);


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

// list (you already have a UI page under /admin/admin-viewservices)
$router->get('/admin/admin-viewservices', [ServicesController::class, 'index']);

// create form + submit
$router->get('/admin/services/create',  [ServicesController::class, 'create']);
$router->post('/admin/services',         [ServicesController::class, 'store']);
// NEW:
$router->get ('/admin/services/{id}/edit',   [ServicesController::class, 'edit']);
$router->post('/admin/services/{id}',        [ServicesController::class, 'update']);
$router->post('/admin/services/{id}/delete', [ServicesController::class, 'destroy']);


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
// Registration form + submit
$router->get('/register', function () {
    require APP_ROOT . '/views/register/index.php'; // form page
});

$router->post('/register', function () {
    require APP_ROOT . '/controllers/register_handler.php'; // <-- correct handler file
});

//Home
use app\controllers\HomeController;

// Landing page (customer)
$router->get('/',            [HomeController::class, 'index']);
$router->get('/home',        [HomeController::class, 'index']); // optional alias

// use app\controllers\customer\DashboardController;

// // Customer dashboard
// $router->get('/customer/dashboard', [DashboardController::class, 'index']);
$router->get('/customer/dashboard', [\app\controllers\customer\DashboardController::class, 'index']);

use app\controllers\customer\RegisteredHomeController;

$router->get('/customer/home',     [RegisteredHomeController::class, 'index']);
$router->get('/registeredhome',    [RegisteredHomeController::class, 'index']); // optional alias

//Customer-Available services
use app\controllers\customer\AvailableServicesController;

$router->get('/customer/available-services', [AvailableServicesController::class, 'index']);
// Alias to match earlier redirect like /services/available?branch=GL
$router->get('/services/available',          [AvailableServicesController::class, 'index']);

//Customer Booking
use app\controllers\customer\BookingController;

$router->get('/customer/book',  [BookingController::class, 'index']);   // booking form
$router->post('/customer/book', [BookingController::class, 'create']);  // (optional) handle submit later

//customer - feedback


$router->get('/customer/rate-service', [\app\controllers\customer\FeedbackController::class, 'index']);
$router->post('/customer/rate-service', [\app\controllers\customer\FeedbackController::class, 'store']); // for saving reviews later



$router->get('/customer/service-history', [\app\controllers\customer\ServiceHistoryController::class, 'index']);

use app\controllers\customer\ServiceReminderController;

$router->get('/customer/service-reminder', [ServiceReminderController::class, 'index']);
$router->post('/customer/service-reminder/update', [ServiceReminderController::class, 'updateMileage']);

use app\controllers\customer\TrackServicesController;

$router->get('/customer/track-services',        [TrackServicesController::class, 'index']);
$router->get('/customer/track-services/list',   [TrackServicesController::class, 'list']); // JSON for AJAX (optional)

use app\controllers\customer\ProfileController;

$router->get('/customer/profile', [ProfileController::class, 'index']);
$router->post('/customer/profile/update', [ProfileController::class, 'updateProfile']);
$router->post('/customer/profile/vehicle', [ProfileController::class, 'saveVehicle']);
$router->post('/customer/profile/vehicle/delete', [ProfileController::class, 'deleteVehicle']);



$router->get ('/customer/appointments',          [\app\controllers\customer\AppointmentsController::class, 'index']);
$router->post('/customer/appointments/cancel',   [\app\controllers\customer\AppointmentsController::class, 'cancel']);   // optional action
$router->get ('/customer/appointments/list',     [\app\controllers\customer\AppointmentsController::class, 'list']);     // optional JSON for AJAX


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
