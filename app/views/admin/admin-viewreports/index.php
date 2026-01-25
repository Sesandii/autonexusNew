<?php
/** @var array $filters */
/** @var array $branches */
/** @var string $reportDataJson */
/** @var string $current */
$current = 'reports';
$B = rtrim(BASE_URL,'/');

$from = htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8');
$to   = htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int)($filters['branch_id'] ?? 0);
$group = htmlspecialchars($filters['group'] ?? 'month', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'AutoNexus • Reports') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/reports/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .main{margin-left:260px;padding:24px;background:#f4f5f7;min-height:100vh;}
    .filter-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:end;margin:14px 0;}
    .filter-bar label{font-size:12px;color:#374151;display:flex;flex-direction:column;gap:4px;}
    .filter-bar select,.filter-bar input{padding:8px 10px;border-radius:10px;border:1px solid #d1d5db;background:#fff;}
    .btn{border:0;border-radius:10px;padding:9px 14px;cursor:pointer;font-weight:600}
    .btn-primary{background:#111827;color:#fff;}
    .btn-ghost{background:#fff;color:#111827;border:1px solid #d1d5db;}
    .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;}
    .tab-btn{padding:10px 14px;border-radius:12px;border:1px solid #d1d5db;background:#fff;cursor:pointer;font-weight:600;}
    .tab-btn.active{background:#111827;color:#fff;border-color:#111827;}
    .grid2{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-top:14px;}
    .box{background:#fff;border-radius:16px;padding:14px;box-shadow:0 1px 4px rgba(15,23,42,.08);}
    .kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:14px;}
    .kpi{background:#fff;border-radius:16px;padding:14px;box-shadow:0 1px 4px rgba(15,23,42,.08);}
    .kpi .label{font-size:12px;color:#6b7280}
    .kpi .value{font-size:22px;font-weight:800;margin-top:4px}
    .chart-box{height:310px;}
    .chart-box canvas{width:100% !important;height:100% !important;}
    .export-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;}
  </style>
</head>

<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<div class="main">
  <h2 style="margin:0 0 10px 0;">Reports</h2>

  <!-- Filters -->
  <form class="filter-bar" method="get" action="<?= $B ?>/admin/admin-viewreports">
    <label>From
      <input type="date" name="from" value="<?= $from ?>">
    </label>
    <label>To
      <input type="date" name="to" value="<?= $to ?>">
    </label>

    <label>Branch
      <select name="branch_id">
        <option value="">All Branches</option>
        <?php foreach ($branches as $br): ?>
          <option value="<?= (int)$br['branch_id'] ?>" <?= $branchId === (int)$br['branch_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($br['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Group By
      <select name="group">
        <option value="month" <?= $group==='month' ? 'selected' : '' ?>>Month</option>
        <option value="day"   <?= $group==='day'   ? 'selected' : '' ?>>Day</option>
      </select>
    </label>

    <button class="btn btn-primary" type="submit">
      <i class="fa-solid fa-rotate"></i> Refresh
    </button>
  </form>

  <!-- Tabs -->
  <div class="tabs">
    <button type="button" class="tab-btn active" data-tab="service">Service</button>
    <button type="button" class="tab-btn" data-tab="revenue">Revenue</button>
    <button type="button" class="tab-btn" data-tab="appointments">Appointments</button>
    <button type="button" class="tab-btn" data-tab="branches">Branch Performance</button>
    <button type="button" class="tab-btn" data-tab="staff">Staff</button>
    <button type="button" class="tab-btn" data-tab="feedback">Feedback</button>
    <button type="button" class="tab-btn" data-tab="approval">Approvals</button>
  </div>

  <!-- KPI row -->
  <div class="kpis" id="kpis"></div>

  <!-- SERVICE TAB -->
  <div class="tab-content" id="tab-service" style="display:block;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Top 10 Services (Completed)</h4>
        <div class="chart-box"><canvas id="chartTopServices"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportTopServices" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportTopServicesPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Service Demand Trend</h4>
        <div class="chart-box"><canvas id="chartServiceTrend"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportServiceTrend" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportServiceTrendPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Service Type Distribution</h4>
        <div class="chart-box"><canvas id="chartServiceTypeDist"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportServiceTypeDist" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportServiceTypeDistPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- REVENUE TAB -->
  <div class="tab-content" id="tab-revenue" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Revenue Trend (Paid Invoices)</h4>
        <div class="chart-box"><canvas id="chartRevenueTrend"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportRevenueTrend" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportRevenueTrendPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Revenue by Branch</h4>
        <div class="chart-box"><canvas id="chartRevenueByBranch"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportRevenueByBranch" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportRevenueByBranchPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Revenue by Service Type</h4>
        <div class="chart-box"><canvas id="chartRevenueByServiceType"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportRevenueByServiceType" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportRevenueByServiceTypePdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- APPOINTMENTS TAB -->
  <div class="tab-content" id="tab-appointments" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Appointment Statuses</h4>
        <div class="chart-box"><canvas id="chartApptStatus"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportApptStatus" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportApptStatusPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Appointments by Hour</h4>
        <div class="chart-box"><canvas id="chartApptByHour"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportApptByHour" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportApptByHourPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Booking Trend</h4>
        <div class="chart-box"><canvas id="chartApptTrend"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportApptTrend" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportApptTrendPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- BRANCH PERFORMANCE TAB -->
  <div class="tab-content" id="tab-branches" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Completed Services per Branch</h4>
        <div class="chart-box"><canvas id="chartBranchCompleted"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportBranchCompleted" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportBranchCompletedPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Average Rating per Branch</h4>
        <div class="chart-box"><canvas id="chartBranchRating"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportBranchRating" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportBranchRatingPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- STAFF TAB -->
  <div class="tab-content" id="tab-staff" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Jobs Completed per Mechanic (Top 10)</h4>
        <div class="chart-box"><canvas id="chartJobsPerMechanic"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportJobsPerMechanic" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportJobsPerMechanicPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Services Submitted (Top 10 Submitters)</h4>
        <div class="chart-box"><canvas id="chartSubmittedByManagers"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportSubmittedByManagers" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportSubmittedByManagersPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- FEEDBACK TAB -->
  <div class="tab-content" id="tab-feedback" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Rating Distribution</h4>
        <div class="chart-box"><canvas id="chartRatingDist"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportRatingDist" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportRatingDistPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Feedback Trend</h4>
        <div class="chart-box"><canvas id="chartFeedbackTrend"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportFeedbackTrend" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportFeedbackTrendPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>

      <div class="box">
        <h4 style="margin:6px 0 10px;">Lowest Rated Services (Avg Rating)</h4>
        <div class="chart-box"><canvas id="chartLowestRated"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportLowestRated" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportLowestRatedPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- APPROVAL TAB -->
  <div class="tab-content" id="tab-approval" style="display:none;">
    <div class="grid2">
      <div class="box">
        <h4 style="margin:6px 0 10px;">Service Approval Status Counts</h4>
        <div class="chart-box"><canvas id="chartApprovalStatus"></canvas></div>
        <div class="export-row">
          <a class="btn btn-ghost" id="exportApprovalStatus" href="#"><i class="fa-solid fa-file-export"></i> CSV</a>
          <a class="btn btn-ghost" id="exportApprovalStatusPdf" href="#"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window.REPORTS_DATA = <?= $reportDataJson ?>;
  window.REPORTS_FILTERS = {
    from: "<?= $from ?>",
    to: "<?= $to ?>",
    branch_id: "<?= (string)$branchId ?>",
    group: "<?= $group ?>"
  };
  window.REPORTS_BASE = "<?= $B ?>";
</script>

<script src="<?= $B ?>/public/assets/js/admin/reports/script.js"></script>
</body>
</html>
