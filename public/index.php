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

/*
|--------------------------------------------------------------------------|
| Build router + routes
|--------------------------------------------------------------------------|
*/
$router = new Router($config ?? []);

/** Dashboard (matches sidebar “/”) */
$router->get('/', [\app\controllers\HomeController::class, 'index']);

/** Branches */
$router->get('/branches',                    [\app\controllers\BranchesController::class, 'index']);
$router->post('/branches',                   [\app\controllers\BranchesController::class, 'store']);
$router->get('/branches/create', [\app\controllers\BranchesController::class, 'create']);

$router->get('/branches/{code}/edit',        [\app\controllers\BranchesController::class, 'edit']);  // ← added
$router->get('/branches/{code}',             [\app\controllers\BranchesController::class, 'show']);  // ← added
$router->post('/branches/update/{code}',     [\app\controllers\BranchesController::class, 'update']); // use {code}, not {id}
$router->post('/branches/delete/{code}',     [\app\controllers\BranchesController::class, 'destroy']); // use {code}, not {id}

/**
 * Service Managers
 * IMPORTANT: specific routes BEFORE dynamic {id} route
 */
$router->get('/service-managers',            [\app\controllers\ServiceManagersController::class, 'index']);
$router->get('/service-managers/list',       [\app\controllers\ServiceManagersController::class, 'list']);
$router->get('/service-managers/create',     [\app\controllers\ServiceManagersController::class, 'create']); // before {id}
$router->post('/service-managers',           [\app\controllers\ServiceManagersController::class, 'store']);
// $router->post('/service-managers/store',  [\app\controllers\ServiceManagersController::class, 'store']); // optional alt

$router->get('/service-managers/{id}/edit',  [\app\controllers\ServiceManagersController::class, 'edit']);   // before {id}
$router->get('/service-managers/{id}',       [\app\controllers\ServiceManagersController::class, 'show']);   // dynamic
$router->post('/service-managers/{id}',      [\app\controllers\ServiceManagersController::class, 'update']);
$router->post('/service-managers/{id}/delete', [\app\controllers\ServiceManagersController::class, 'destroy']);

/** Dev helper */
$router->get('/test-managers', function () {
    require_once __DIR__ . '/../app/model/Manager.php';
    $m = new \app\model\Manager();
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
$router->get('/customers',         fn() => print 'TODO: Customers page');
$router->get('/supervisors',       fn() => print 'TODO: Supervisors page');
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
