<?php
declare(strict_types=1);
session_start();


date_default_timezone_set('Asia/Colombo');   // 👈 add this line

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


// Composer autoload (Dompdf, PHPMailer, etc.)
$autoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}



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
$router->post('/admin/customers/{id}/deactivate', [\app\controllers\Admin\CustomersController::class, 'deactivate']);
$router->post('/admin/customers/{id}/activate',   [\app\controllers\Admin\CustomersController::class, 'activate']);

/** ======================
 *  ADMIN: Receptionists (route by code)
 *  ====================== */

$router->get('/admin/viewreceptionist', [\app\controllers\Admin\ReceptionistsController::class, 'index']);
// Receptionists (admin)
$router->get ('/admin/receptionists/create',        [\app\controllers\Admin\ReceptionistsController::class, 'create']);
$router->post('/admin/receptionists/create',        [\app\controllers\Admin\ReceptionistsController::class, 'create']);   // same method handles POST
$router->get ('/admin/receptionists/show',          [\app\controllers\Admin\ReceptionistsController::class, 'show']);
$router->get ('/admin/receptionists/edit',          [\app\controllers\Admin\ReceptionistsController::class, 'edit']);
$router->post('/admin/receptionists/edit',          [\app\controllers\Admin\ReceptionistsController::class, 'edit']);     // same method handles POST
$router->post('/admin/receptionists/delete',        [\app\controllers\Admin\ReceptionistsController::class, 'delete']);

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

// Optional prettier alias for the list page
$router->get('/admin/appointments', [AppointmentsController::class, 'index']);

// Show details page
$router->get('/admin/admin-appointments/show',  [AppointmentsController::class, 'show']);

// Edit form
$router->get('/admin/admin-appointments/edit',  [AppointmentsController::class, 'edit']);

// Handle update form submit
$router->post('/admin/admin-appointments/update', [AppointmentsController::class, 'update']);

// Handle cancel/delete
$router->post('/admin/admin-appointments/delete', [AppointmentsController::class, 'delete']);


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

$router->get('/admin/admin-updateserviceprice',  [PricingController::class, 'index']);
$router->post('/admin/admin-updateserviceprice', [PricingController::class, 'index']);



use app\controllers\admin\ApprovalController;

// ...
$router->get('/admin/admin-serviceapproval', [ApprovalController::class, 'index']);
$router->get ('/admin/admin-serviceapproval/show',  [ApprovalController::class, 'show']);
$router->get ('/admin/admin-serviceapproval/edit',  [ApprovalController::class, 'edit']);
$router->post('/admin/admin-serviceapproval/update', [ApprovalController::class, 'update']);


use app\controllers\admin\OngoingServicesController;

$router->get('/admin/admin-ongoingservices', [OngoingServicesController::class, 'index']);
$router->get('/admin/admin-ongoingservices/show', [OngoingServicesController::class, 'show']);


use app\controllers\admin\ServiceHistoryController;

$router->get('/admin/admin-servicehistory', [ServiceHistoryController::class, 'index']);
$router->get('/admin/admin-servicehistory/show', [ServiceHistoryController::class, 'show']);


use app\controllers\admin\FeedbackController;

$router->get('/admin/admin-viewfeedback', [FeedbackController::class, 'index']);
$router->post('/admin/admin-viewfeedback/reply', [FeedbackController::class, 'reply']);


use app\controllers\admin\NotificationsController;

$router->get('/admin/admin-notifications', [NotificationsController::class, 'index']);
$router->post('/admin/admin-notifications/send', [\app\controllers\admin\NotificationsController::class, 'send']);
$router->get('/admin/admin-notifications/users', [\app\controllers\admin\NotificationsController::class, 'users']); // for recipient picker
$router->post('/admin/admin-notifications/run-daily', [\app\controllers\admin\NotificationsController::class, 'runDaily']); // optional cron trigger




use app\controllers\admin\InvoicesController;


$router->get('/admin/admin-viewinvoices',              [InvoicesController::class, 'index']);

$router->get('/admin/admin-viewinvoices/create',       [InvoicesController::class, 'create']);
$router->post('/admin/admin-viewinvoices/store',       [InvoicesController::class, 'store']);

$router->get('/admin/admin-viewinvoices/show',         [InvoicesController::class, 'show']);

$router->get('/admin/admin-viewinvoices/download',     [InvoicesController::class, 'download']);

// optional (only if you want emailing enabled)
// $router->get('/admin/admin-viewinvoices/email',        [InvoicesController::class, 'email']);




use app\controllers\admin\ReportsController;

$router->get('/admin/admin-viewreports', [ReportsController::class, 'index']);




$router->get('/admin/admin-viewreports',              [ReportsController::class, 'index']);
$router->get('/admin/admin-viewreports/export',       [ReportsController::class, 'export']);
$router->get('/admin/admin-viewreports/export-pdf',   [ReportsController::class, 'exportPdf']);


use app\controllers\admin\ComplaintsController;

// Old-style route
$router->get('/admin/admin-viewcomplaints', [ComplaintsController::class, 'index']);
$router->get('/admin/admin-viewcomplaints/show', [ComplaintsController::class, 'show']);
$router->post('/admin/admin-viewcomplaints/update', [ComplaintsController::class, 'update']);

// Clean alias routes for testing
$router->get('/admin/complaints', [ComplaintsController::class, 'index']);
$router->get('/admin/complaints/show', [ComplaintsController::class, 'show']);
$router->post('/admin/complaints/update', [ComplaintsController::class, 'update']);




$router->get('/logout', [\app\controllers\LogoutController::class, 'index']);



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
$router->get('/customer/book/slots',  [BookingController::class, 'slots']);

//customer - feedback


$router->get('/customer/rate-service', [\app\controllers\customer\FeedbackController::class, 'index']);
$router->post('/customer/rate-service', [\app\controllers\customer\FeedbackController::class, 'store']); // for saving reviews later
$router->get('/customer/reviews', [\app\controllers\customer\FeedbackController::class, 'index']);



$router->get('/customer/service-history', [\app\controllers\customer\ServiceHistoryController::class, 'index']);
$router->get('/customer/service-history/{id}', [\app\controllers\customer\ServiceHistoryController::class, 'show']);
$router->get('/customer/service-history/{id}/pdf', [\app\controllers\customer\ServiceHistoryController::class, 'downloadPdf']);
use app\controllers\customer\ServiceReminderController;

$router->get('/customer/service-reminder', [ServiceReminderController::class, 'index']);
$router->post('/customer/service-reminder/update', [ServiceReminderController::class, 'updateMileage']);

use app\controllers\customer\TrackServicesController;

$router->get('/customer/track-services',        [TrackServicesController::class, 'index']);
$router->get('/customer/track-services/list',   [TrackServicesController::class, 'list']); // JSON for AJAX (optional)

// use app\controllers\customer\ProfileController;

// $router->get('/customer/profile', [ProfileController::class, 'index']);
// $router->post('/customer/profile/update', [ProfileController::class, 'updateProfile']);
// $router->post('/customer/profile/vehicle', [ProfileController::class, 'saveVehicle']);
// $router->post('/customer/profile/vehicle/delete', [ProfileController::class, 'deleteVehicle']);

// Profile (HTML forms, no JSON)
$router->get('/customer/profile',                 [\app\controllers\customer\ProfileController::class, 'index']);
$router->get('/customer/profile/edit',            [\app\controllers\customer\ProfileController::class, 'editForm']);
$router->post('/customer/profile/update',         [\app\controllers\customer\ProfileController::class, 'updateProfile']);

$router->get('/customer/profile/vehicle',         [\app\controllers\customer\ProfileController::class, 'vehicleForm']); // add OR edit by ?id=
$router->post('/customer/profile/vehicle',        [\app\controllers\customer\ProfileController::class, 'saveVehicle']);
$router->post('/customer/profile/vehicle/delete', [\app\controllers\customer\ProfileController::class, 'deleteVehicle']);



$router->get ('/customer/appointments',          [\app\controllers\customer\AppointmentsController::class, 'index']);

$router->get ('/customer/appointments/{id}',     [\app\controllers\customer\AppointmentsController::class, 'show']);

$router->get ('/customer/appointments/{id}',     [\app\controllers\customer\AppointmentsController::class, 'show']);
$router->post('/customer/appointments/cancel',   [\app\controllers\customer\AppointmentsController::class, 'cancel']);   // optional action
$router->get ('/customer/appointments/list',     [\app\controllers\customer\AppointmentsController::class, 'list']);     // optional JSON for AJAX

// Customer - File Complaint
use app\controllers\customer\FileComplaintController;

$router->get('/customer/file-complaint',         [FileComplaintController::class, 'index']);
$router->post('/customer/file-complaint/submit', [FileComplaintController::class, 'submit']);
$router->get('/customer/complaints/history',     [FileComplaintController::class, 'history']); // optional: view complaint history


$router->post('/customer/appointments/cancel',   [\app\controllers\customer\AppointmentsController::class, 'cancel']);   // optional action
$router->get ('/customer/appointments/list',     [\app\controllers\customer\AppointmentsController::class, 'list']);     // optional JSON for AJAX

// // Customer - File Complaint
// use app\controllers\customer\FileComplaintController;

// $router->get('/customer/file-complaint',         [FileComplaintController::class, 'index']);
// $router->post('/customer/file-complaint/submit', [FileComplaintController::class, 'submit']);
// $router->get('/customer/complaints/history',     [FileComplaintController::class, 'history']); // optional: view complaint history


// $router->post('/customer/appointments/cancel',   [\app\controllers\customer\AppointmentsController::class, 'cancel']);   // optional action
// $router->get ('/customer/appointments/list',     [\app\controllers\customer\AppointmentsController::class, 'list']);     // optional JSON for AJAX


//manager: Dashboard
$router->get('/manager/dashboard', [app\controllers\Manager\DashboardController::class, 'index']);

//Manager: Appointments
$router->get('/manager/appointments', [app\controllers\Manager\AppointmentsController::class, 'index']);
$router->get('/manager/appointments/new',         [app\controllers\Manager\AppointmentsController::class, 'create']);
$router->get('/manager/appointments/day', [app\controllers\Manager\AppointmentsController::class, 'day']);
$router->post('/manager/appointments/save',       [app\controllers\Manager\AppointmentsController::class, 'save']);
$router->get('/manager/appointments/getCustomer', [app\controllers\Manager\AppointmentsController::class, 'getCustomer']);
$router->get('/manager/appointments/getServices', [app\controllers\Manager\AppointmentsController::class, 'getServices']);
$router->get('/manager/appointments/edit/{id}', [app\controllers\Manager\AppointmentsController::class, 'edit']);

//Manager: Services
$router->get('/manager/services',        [app\controllers\Manager\ServicesController::class, 'index']); 
$router->get('/manager/services/create', [app\controllers\Manager\ServicesController::class,'create']);
$router->post('/manager/services/store', [app\controllers\Manager\ServicesController::class,'store']);

//Manager: Billing
$router->get('/manager/billing', [app\controllers\Manager\BillingController::class, 'invoices']);
$router->get('/manager/billing/downloadInvoice/{id}',[app\controllers\Manager\BillingController::class, 'downloadInvoice']);

//Manager: Complaints
$router->get('/manager/complaints',                         [app\controllers\Manager\ComplaintController::class, 'index']);
$router->get('/manager/complaints/{complaintId}',           [app\controllers\Manager\ComplaintController::class, 'show']);

//Manager: Customer Profile
$router->get('/manager/customers',                      [app\controllers\Manager\CustomerController::class, 'index']);
$router->get('/manager/customers/{customerId}',         [app\controllers\Manager\CustomerController::class, 'show']);

// Manager: Team Schedule
$router->get('/manager/schedule', [app\controllers\Manager\ScheduleController::class,'index']);
$router->get('/manager/schedule/day', [app\controllers\Manager\ScheduleController::class,'day']);
$router->get('/manager/schedule/member', [app\controllers\Manager\ScheduleController::class, 'member']);

// Manager: Peformance
$router->get('/manager/performance', [\app\controllers\Manager\PerformanceController::class,'index']);
$router->get('/manager/performance/stats', [\app\controllers\Manager\PerformanceController::class,'stats']);
$router->get('/manager/performance/team', [\app\controllers\Manager\PerformanceController::class,'team']);
$router->get('/manager/performance/jobsByDay', [\app\controllers\Manager\PerformanceController::class,'jobsByDay']);
$router->get('/manager/performance/mechanics', [\app\controllers\Manager\PerformanceController::class, 'mechanics']);
$router->get('/manager/performance/viewMechanic',[\app\controllers\Manager\PerformanceController::class, 'viewMechanic']);

// manager: reports
$router->get('/manager/reports', [app\controllers\Manager\ReportController::class, 'index']);
$router->get('/manager/reports/getFilters', [app\controllers\Manager\ReportController::class, 'getFilters']);
$router->post('/manager/reports/generate', [app\controllers\Manager\ReportController::class, 'generate']);
$router->post('/manager/reports/result', [app\controllers\Manager\ReportController::class, 'result']);

//Receptionist: Complaints

$router->get('/receptionist/complaints',                         [app\controllers\Receptionist\ComplaintController::class, 'index']);
$router->get('/receptionist/complaints/new',                     [app\controllers\Receptionist\ComplaintController::class, 'create']);
$router->get('/receptionist/complaints/fetchByPhone',            [app\controllers\Receptionist\ComplaintController::class, 'fetchByPhone']); // important: before dynamic
$router->post('/receptionist/complaints',                        [app\controllers\Receptionist\ComplaintController::class, 'store']);
$router->get('/receptionist/complaints/{complaintId}',           [app\controllers\Receptionist\ComplaintController::class, 'show']);
$router->get('/receptionist/complaints/history/{customer_name}', [app\controllers\Receptionist\ComplaintController::class, 'history']);
$router->get('/receptionist/complaints/edit/{id}',               [app\controllers\Receptionist\ComplaintController::class, 'edit']);
$router->post('/receptionist/complaints/update/{id}',            [app\controllers\Receptionist\ComplaintController::class, 'update']);
$router->get('/receptionist/complaints/delete/{id}',             [app\controllers\Receptionist\ComplaintController::class, 'delete']);

//Receptionist: Appointments

$router->get('/receptionist/appointments',             [app\controllers\Receptionist\AppointmentsController::class, 'index']);
$router->get('/receptionist/appointments/new',         [app\controllers\Receptionist\AppointmentsController::class, 'create']);
$router->get('/receptionist/appointments/day',         [app\controllers\Receptionist\AppointmentsController::class, 'day']);
$router->post('/receptionist/appointments/save',       [app\controllers\Receptionist\AppointmentsController::class, 'save']);
$router->get('/receptionist/appointments/getCustomer', [app\controllers\Receptionist\AppointmentsController::class, 'getCustomer']);
$router->get('/receptionist/appointments/getServices', [app\controllers\Receptionist\AppointmentsController::class, 'getServices']);
$router->post('/receptionist/appointments/assignSupervisor', [app\controllers\Receptionist\AppointmentsController::class, 'assignSupervisor']);
$router->get('/receptionist/appointments/edit/{id}', [app\controllers\Receptionist\AppointmentsController::class, 'edit']);
$router->post('/receptionist/appointments/update', [app\controllers\Receptionist\AppointmentsController::class, 'update']);


//Receptionist: Customer Profiles

$router->get('/receptionist/customers',                      [app\controllers\Receptionist\CustomerController::class, 'index']); // Customer Profiles list
$router->get('/receptionist/customers/new',                  [app\controllers\Receptionist\CustomerController::class, 'create']); // Show new customer form
$router->post('/receptionist/customers/store',               [app\controllers\Receptionist\CustomerController::class, 'store']);// store customer detail
$router->get('/receptionist/customers/{customerId}',         [app\controllers\Receptionist\CustomerController::class, 'show']);
$router->get('/receptionist/customers/edit/{customerId}',    [app\controllers\Receptionist\CustomerController::class, 'edit']);
$router->post('/receptionist/customers/update/{customerId}', [app\controllers\Receptionist\CustomerController::class, 'updateCustomer']);

//Receptionist: Dashboard

$router->get('/receptionist/dashboard', [app\controllers\Receptionist\ReceptionistD::class, 'index']);
//Receptionist: Services

$router->get('/receptionist/service', [app\controllers\Receptionist\ServiceController::class, 'index']);

//Receptionist: Billing

$router->get('/receptionist/billing', [app\controllers\Receptionist\BillingController::class, 'invoices']);   // Index page
$router->get('/receptionist/billing/create',          [app\controllers\Receptionist\BillingController::class, 'create']);
$router->get('/receptionist/billing/invoice/{id}',    [app\controllers\Receptionist\BillingController::class, 'preview']);
$router->post('/receptionist/billing/invoice/{id}',   [app\controllers\Receptionist\BillingController::class, 'store']); // Invoice creation page
$router->get('/receptionist/billing/downloadInvoice/{id}',[app\controllers\Receptionist\BillingController::class, 'downloadInvoice']);
$router->get('/receptionist/billing/paid',[app\controllers\Receptionist\BillingController::class, 'paidInvoices']);






// --------------Mechanic copy
use app\controllers\mechanic\MechanicProfileController;
$router->get('/mechanic/profile/edit', [MechanicProfileController::class, 'edit']);
$router->post('/mechanic/profile/update', [MechanicProfileController::class, 'update']);

$router->get('/mechanic/jobs', [\app\controllers\mechanic\JobsMController::class, 'index']);
$router->get('/mechanic/assignedjobs', [\app\controllers\mechanic\AssignedJobsMController::class, 'index']);
$router->get('/mechanic/dashboard', [\app\controllers\mechanic\DashboardController::class, 'index']);

use app\controllers\mechanic\HistoryController;
$router->get('/mechanic/history', [HistoryController::class, 'index']);
$router->get('/mechanic/history/show', [HistoryController::class, 'show']);
$router->get('/mechanic/history/details/{appointmentId}', [HistoryController::class, 'details']);


use app\controllers\mechanic\JobsMVController;
$router->get('/mechanic/jobs/view/{id}', [JobsMVController::class, 'show']);
$router->post('/mechanic/jobs/update-status', [JobsMVController::class, 'updateStatus']);

// ------------------Supervisor copy
$router->get('/supervisor/dashboard', [\app\controllers\supervisor\SupervisorController::class, 'index']);
$router->get('/supervisor/complaints_feedbacks', [\app\controllers\supervisor\ComplaintsFeedbackController::class, 'index']);

use app\controllers\supervisor\SupervisorProfileController;
$router->get('/supervisor/profile/edit', [SupervisorProfileController::class, 'edit']);
$router->post('/supervisor/profile/update', [SupervisorProfileController::class, 'update']);

use app\controllers\supervisor\VehicleReportsController;
$router->get('/supervisor/reports', [VehicleReportsController::class, 'index']);
$router->get('/supervisor/reports/daily-jobs', [VehicleReportsController::class, 'dailyJobs']);
$router->get('/supervisor/reports/indexp', [VehicleReportsController::class, 'indexp']);
$router->get('/supervisor/reports/mechanic-activity', [VehicleReportsController::class, 'mechanicActivity']);
$router->get('/supervisor/reports/create', [VehicleReportsController::class, 'create']);
$router->get('/supervisor/reports/view/{id}', [VehicleReportsController::class,'show']);
$router->post('/supervisor/reports/store', [VehicleReportsController::class, 'store']);
$router->get('/supervisor/reports/edit/{id}', [VehicleReportsController::class,'edit']);
$router->post('/supervisor/reports/update/{reportId}', [VehicleReportsController::class,'update']);
$router->post('/supervisor/reports/delete/{id}', [VehicleReportsController::class, 'delete']);
$router->get('/supervisor/reports/delete-photo/{id}',[VehicleReportsController::class, 'deletePhoto']);

use app\controllers\supervisor\CoordinationController;
$router->get('/supervisor/coordination', [CoordinationController::class, 'index']);
$router->post('/supervisor/coordination/updateMechanicStatus', [CoordinationController::class, 'updateMechanicStatus']);
$router->post('/supervisor/coordination/reportIssue', [CoordinationController::class, 'reportIssue']);



use \app\controllers\supervisor\VehicleHistoryController;
$router->get('/supervisor/history', [VehicleHistoryController::class, 'index']);
$router->get('/supervisor/history/show', [VehicleHistoryController::class, 'show']);
$router->get('/supervisor/history/details/{appointmentId}', [VehicleHistoryController::class, 'details']);

use app\controllers\supervisor\AssignedJobsController;
$router->get('/supervisor/assignedjobs', [AssignedJobsController::class, 'index']);
$router->get('/supervisor/assignedjobs/{id}', [AssignedJobsController::class, 'edit']);
$router->post('/supervisor/checklist/toggle', [AssignedJobsController::class, 'toggleChecklist']);
$router->post('/supervisor/assignedjobs/uploadPhoto',[AssignedJobsController::class, 'uploadPhoto']);
$router->post('/supervisor/assignedjobs/deletePhoto',[AssignedJobsController::class, 'deletePhoto']);

use app\controllers\supervisor\WorkOrdersController;
$router->get ('/supervisor/workorders',           [WorkOrdersController::class, 'index']);
$router->get ('/supervisor/workorders/create',    [WorkOrdersController::class, 'createForm']);
$router->post('/supervisor/workorders',           [WorkOrdersController::class, 'store']);
$router->get ('/supervisor/workorders/{id}',      [WorkOrdersController::class, 'show']);
$router->get ('/supervisor/workorders/{id}/edit', [WorkOrdersController::class, 'editForm']);
$router->post('/supervisor/workorders/{id}',      [WorkOrdersController::class, 'update']);
$router->post('/supervisor/workorders/{id}/delete', [WorkOrdersController::class, 'destroy']);

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
