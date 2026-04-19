<?php /* Admin view: renders admin-dashboard/index page. */ ?>
<?php
$current = 'dashboard';

$B = rtrim(BASE_URL, '/');
$adminName = trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?: 'Admin User';

/*
|--------------------------------------------------------------------------
| Route map
|--------------------------------------------------------------------------
| I updated the routes you said were failing using the older AutoNexus
| admin naming pattern that already exists in your project.
| If one specific module still has a different URL in your system,
| change only the value here.
*/
$routes = [
    'customers'          => $B . '/admin/customers',
    'appointments'       => $B . '/admin/appointments',
    'ongoing_services'   => $B . '/admin/admin-ongoingservices',
    'service_history'    => $B . '/admin/admin-servicehistory',
    'payments'            => $B . '/admin/admin-viewpayments',
    'reports'            => $B . '/admin/admin-viewreports',
    'feedback'           => $B . '/admin/admin-viewfeedback',
    'complaints'         => $B . '/admin/admin-viewcomplaints',
    'notifications'      => $B . '/admin/admin-notifications',
    'approvals'          => $B . '/admin/admin-serviceapproval',
    'branches_create'    => $B . '/admin/branches/create',
    'services_create'    => $B . '/admin/services/create',
    'mechanics_create'   => $B . '/admin/mechanics/create',
    'supervisors_create' => $B . '/admin/supervisors/create',
];

function e($value): string
{
    return htmlspecialchars((string)$value);
}

function firstWords(?string $text, int $max = 10): string
{
    $text = trim((string)$text);
    if ($text === '') {
        return '—';
    }

    $words = preg_split('/\s+/', $text);
    if (!$words) {
        return '—';
    }

    if (count($words) <= $max) {
        return $text;
    }

    return implode(' ', array_slice($words, 0, $max)) . '...';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoNexus • Dashboard</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-dashboard.css?v=4">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-dashboardindex.css?v=1">
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="topbar">
      <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="subtitle">Overview of your AutoNexus service station</p>
        <?php require APP_ROOT . '/views/partials/lang-switcher.php'; ?>
      </div>

      <a class="user-chip user-chip--link" href="<?= $B ?>/admin/profile" aria-label="Open profile">
        <div class="avatar"><i class="fa-solid fa-user"></i></div>
        <span><?= e($adminName) ?></span>
      </a>
    </header>

    <section class="dash-wrap">
      <div class="kpi-grid">
        <a class="kpi-card-link" href="<?= $routes['customers'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
            <div class="kpi-meta">
              <h3>Total Active Customers</h3>
              <p class="kpi-value"><?= number_format((int)($metrics['customers'] ?? 0)) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $routes['appointments'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
            <div class="kpi-meta">
              <h3>Total Appointments</h3>
              <p class="kpi-value"><?= number_format((int)($metrics['appointments'] ?? 0)) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $routes['ongoing_services'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-spinner"></i></div>
            <div class="kpi-meta">
              <h3>Ongoing Work Orders</h3>
              <p class="kpi-value"><?= number_format((int)($metrics['ongoing'] ?? 0)) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $routes['service_history'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-meta">
              <h3>Services Completed</h3>
              <p class="kpi-value"><?= number_format((int)($metrics['completed'] ?? 0)) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $routes['payments'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="kpi-meta">
              <h3>Total Revenue</h3>
              <p class="kpi-value">Rs.<?= number_format((float)($metrics['revenue'] ?? 0), 2) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $routes['feedback'] ?>">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-regular fa-message"></i></div>
            <div class="kpi-meta">
              <h3>Feedback Count</h3>
              <p class="kpi-value"><?= number_format((int)($metrics['feedback'] ?? 0)) ?></p>
              <span class="kpi-delta up">Open module</span>
            </div>
          </article>
        </a>
      </div>

      <div class="compact-grid">
        <section class="panel">
          <div class="panel-head">
            <h2>Today’s Appointments</h2>
            <a class="panel-link" href="<?= $routes['appointments'] ?>">View All</a>
          </div>

          <?php if (!empty($todayAppointments)): ?>
            <div class="table-wrap">
              <table class="table">
                <thead>
                  <tr>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Branch</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($todayAppointments as $a): ?>
                    <?php $appointmentUrl = $routes['appointments'] . '?id=' . urlencode((string)$a['appointment_id']); ?>
                    <tr>
                      <td><a class="row-link" href="<?= $appointmentUrl ?>"><?= e($a['appointment_time'] ?? '—') ?></a></td>
                      <td><a class="row-link" href="<?= $appointmentUrl ?>"><?= e($a['customer_name'] ?? '—') ?></a></td>
                      <td><a class="row-link" href="<?= $appointmentUrl ?>"><?= e($a['service_name'] ?? '—') ?></a></td>
                      <td><a class="row-link" href="<?= $appointmentUrl ?>"><?= e($a['branch_name'] ?? '—') ?></a></td>
                      <td><a class="row-link" href="<?= $appointmentUrl ?>"><span class="status-pill"><?= e($a['status'] ?? '—') ?></span></a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="empty-state">No appointments scheduled for today.</p>
          <?php endif; ?>
        </section>

        <aside class="quick-links card">
          <h2>Quick Links</h2>
          <div class="ql-grid">
            <a class="ql-card" href="<?= $routes['branches_create'] ?>">
              <i class="fa-solid fa-building"></i>
              <span>Add Branch</span>
            </a>

            <a class="ql-card" href="<?= $routes['services_create'] ?>">
              <i class="fa-solid fa-screwdriver-wrench"></i>
              <span>Add Service</span>
            </a>

            <a class="ql-card" href="<?= $routes['mechanics_create'] ?>">
              <i class="fa-solid fa-user-gear"></i>
              <span>Add Mechanic</span>
            </a>

            <a class="ql-card" href="<?= $routes['supervisors_create'] ?>">
              <i class="fa-solid fa-user-tie"></i>
              <span>Add Supervisor</span>
            </a>

            <a class="ql-card" href="<?= $routes['appointments'] ?>">
              <i class="fa-regular fa-calendar-check"></i>
              <span>View Appointments</span>
            </a>

            <a class="ql-card" href="<?= $routes['reports'] ?>">
              <i class="fa-solid fa-chart-simple"></i>
              <span>View Reports</span>
            </a>
          </div>
        </aside>
      </div>

      <div class="triple-grid">
        <section class="panel">
          <div class="panel-head">
            <h2>Pending Service Approvals</h2>
            <a class="panel-link" href="<?= $routes['approvals'] ?>">View All</a>
          </div>

          <a class="mini-item" href="<?= $routes['approvals'] ?>">
            <div class="mini-item-top">
              <p class="mini-item-title">Services awaiting approval</p>
              <span class="mini-badge warn"><?= (int)($pendingApprovals ?? 0) ?></span>
            </div>
            <p class="mini-item-sub">Open the approval page to review pending service submissions.</p>
          </a>
        </section>

        <section class="panel">
          <div class="panel-head">
            <h2>Work Orders</h2>
            <a class="panel-link" href="<?= $routes['ongoing_services'] ?>">View All</a>
          </div>

          <?php if (!empty($overdueWorkOrders)): ?>
            <div class="mini-list">
              <?php foreach ($overdueWorkOrders as $wo): ?>
                <?php $workOrderUrl = $routes['ongoing_services'] . '?id=' . urlencode((string)$wo['work_order_id']); ?>
                <a class="mini-item" href="<?= $workOrderUrl ?>">
                  <div class="mini-item-top">
                    <p class="mini-item-title">Work Order #<?= e($wo['work_order_id']) ?></p>
                    <span class="mini-badge danger"><?= e($wo['status'] ?? '—') ?></span>
                  </div>
                  <p class="mini-item-sub"><?= e($wo['customer_name'] ?? '—') ?> · <?= e($wo['service_name'] ?? '—') ?></p>
                  <p class="mini-item-sub"><?= e($wo['branch_name'] ?? '—') ?></p>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty-state">No work orders found.</p>
          <?php endif; ?>
        </section>

        <section class="panel">
          <div class="panel-head">
            <h2>Recent Notifications</h2>
            <a class="panel-link" href="<?= $routes['notifications'] ?>">View All</a>
          </div>

          <?php if (!empty($recentNotifications)): ?>
            <div class="mini-list">
              <?php foreach ($recentNotifications as $n): ?>
                <?php $notificationUrl = $routes['notifications'] . '?id=' . urlencode((string)$n['notification_id']); ?>
                <a class="mini-item" href="<?= $notificationUrl ?>">
                  <div class="mini-item-top">
                    <p class="mini-item-title"><?= e(firstWords($n['subject'] ?? '—', 7)) ?></p>
                    <span class="mini-badge"><?= e($n['status'] ?? '—') ?></span>
                  </div>
                  <p class="mini-item-sub">Audience: <?= e($n['audience'] ?? '—') ?></p>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty-state">No recent notifications available.</p>
          <?php endif; ?>
        </section>
      </div>

      <div class="double-grid">
        <section class="panel">
          <div class="panel-head">
            <h2>Recent Complaints</h2>
            <a class="panel-link" href="<?= $routes['complaints'] ?>">View All</a>
          </div>

          <?php if (!empty($recentComplaints)): ?>
            <div class="mini-list">
              <?php foreach ($recentComplaints as $c): ?>
                <?php $complaintUrl = $routes['complaints'] . '?id=' . urlencode((string)$c['complaint_id']); ?>
                <a class="mini-item" href="<?= $complaintUrl ?>">
                  <div class="mini-item-top">
                    <p class="mini-item-title"><?= e(firstWords($c['subject'] ?? '—', 8)) ?></p>
                    <span class="mini-badge <?= ($c['priority'] ?? '') === 'high' ? 'danger' : 'warn' ?>"><?= e($c['priority'] ?? '—') ?></span>
                  </div>
                  <p class="mini-item-sub"><?= e($c['customer_name'] ?? '—') ?></p>
                  <p class="mini-item-sub">Status: <?= e($c['status'] ?? '—') ?></p>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty-state">No recent complaints found.</p>
          <?php endif; ?>
        </section>

        <section class="panel">
          <div class="panel-head">
            <h2>Recent Feedback</h2>
            <a class="panel-link" href="<?= $routes['feedback'] ?>">View All</a>
          </div>

          <?php if (!empty($recentFeedback)): ?>
            <div class="mini-list">
              <?php foreach ($recentFeedback as $f): ?>
                <?php $feedbackUrl = $routes['feedback'] . '?id=' . urlencode((string)$f['feedback_id']); ?>
                <a class="mini-item" href="<?= $feedbackUrl ?>">
                  <div class="mini-item-top">
                    <p class="mini-item-title"><?= e($f['customer_name'] ?? '—') ?></p>
                    <span class="mini-badge success">Rating: <?= e($f['rating'] ?? '—') ?></span>
                  </div>
                  <p class="mini-item-sub"><?= e(firstWords($f['comment'] ?? '—', 10)) ?></p>
                  <p class="mini-item-sub">Reply Status: <?= e($f['replied_status'] ?? '—') ?></p>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty-state">No recent feedback found.</p>
          <?php endif; ?>
        </section>
      </div>
    </section>
  </main>
</body>
</html>