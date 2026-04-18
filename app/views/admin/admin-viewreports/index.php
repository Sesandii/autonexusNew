<?php
$current = 'reports';
$B = rtrim(BASE_URL, '/');

$from = htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8');
$to = htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int) ($filters['branch_id'] ?? 0);
$group = htmlspecialchars($filters['group'] ?? 'month', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'AutoNexus • Reports') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/reports/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .main {
      margin-left: 260px;
      padding: 24px;
      background: #f4f5f7;
      min-height: 100vh;
    }

    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: end;
      margin: 14px 0;
    }

    .filter-bar label {
      font-size: 12px;
      color: #374151;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .filter-bar select,
    .filter-bar input {
      padding: 8px 10px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      background: #fff;
    }

    .btn {
      border: 0;
      border-radius: 10px;
      padding: 9px 14px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px
    }

    .btn-primary {
      background: #111827;
      color: #fff;
    }

    .btn-ghost {
      background: #fff;
      color: #111827;
      border: 1px solid #d1d5db;
    }

    .tabs {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .tab-btn {
      padding: 10px 14px;
      border-radius: 12px;
      border: 1px solid #d1d5db;
      background: #fff;
      cursor: pointer;
      font-weight: 600;
    }

    .tab-btn.active {
      background: #111827;
      color: #fff;
      border-color: #111827;
    }

    .grid2 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 16px;
      margin-top: 14px;
    }

    .box {
      background: #fff;
      border-radius: 16px;
      padding: 14px;
      box-shadow: 0 1px 4px rgba(15, 23, 42, .08);
    }

    .kpis {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin-top: 14px;
    }

    .kpi {
      background: #fff;
      border-radius: 16px;
      padding: 14px;
      box-shadow: 0 1px 4px rgba(15, 23, 42, .08);
    }

    .kpi .label {
      font-size: 12px;
      color: #6b7280
    }

    .kpi .value {
      font-size: 22px;
      font-weight: 800;
      margin-top: 4px
    }

    .chart-box {
      height: 310px;
    }

    .chart-box canvas {
      width: 100% !important;
      height: 100% !important;
    }

    .export-row {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .section-title {
      margin: 22px 0 6px;
      font-size: 20px;
      font-weight: 800;
      color: #111827
    }
  </style>
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <div class="main">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
      <h2 style="margin:0;">Reports & Analytics</h2>
      <div style="display:flex;gap:8px;">
        <a href="<?= $B ?>/admin/admin-viewreports/export-all?format=pdf&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
          class="btn btn-primary" title="Download all reports as PDF">
          <i class="fa-solid fa-file-pdf"></i> Download PDF
        </a>
        <a href="<?= $B ?>/admin/admin-viewreports/export-all?format=csv&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
          class="btn btn-primary" title="Download all reports as CSV">
          <i class="fa-solid fa-file-csv"></i> Download CSV
        </a>
      </div>
    </div>

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
            <option value="<?= (int) $br['branch_id'] ?>" <?= $branchId === (int) $br['branch_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($br['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Group By
        <select name="group">
          <option value="month" <?= $group === 'month' ? 'selected' : '' ?>>Month</option>
          <option value="day" <?= $group === 'day' ? 'selected' : '' ?>>Day</option>
        </select>
      </label>

      <button class="btn btn-primary" type="submit">
        <i class="fa-solid fa-rotate"></i> Refresh
      </button>
    </form>

    <div class="tabs">
      <button type="button" class="tab-btn active" data-tab="service">Service</button>
      <button type="button" class="tab-btn" data-tab="revenue">Revenue</button>
      <button type="button" class="tab-btn" data-tab="appointments">Appointments</button>
      <button type="button" class="tab-btn" data-tab="branches">Branch Performance</button>
      <button type="button" class="tab-btn" data-tab="staff">Staff</button>
      <button type="button" class="tab-btn" data-tab="feedback">Feedback</button>
      <button type="button" class="tab-btn" data-tab="approval">Approvals</button>
      <button type="button" class="tab-btn" data-tab="complaints">Complaints</button>
    </div>

    <style>
      .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
      }

      .report-header h4 {
        margin: 0;
        flex: 1;
      }

      .report-downloads {
        display: flex;
        gap: 6px;
      }

      .download-btn {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
      }

      .download-btn:hover {
        background: #f3f4f6;
        color: #111827;
      }

      .download-btn.pdf {
        color: #dc2626;
        border-color: #fecaca;
      }

      .download-btn.pdf:hover {
        background: #fee2e2;
      }

      .download-btn.csv {
        color: #059669;
        border-color: #a7f3d0;
      }

      .download-btn.csv:hover {
        background: #ecfdf5;
      }
    </style>

    <div class="kpis" id="kpis"></div>

    <div class="tab-content" id="tab-service" style="display:block;">
      <div class="section-title">Service Analytics</div>
      <div class="grid2">
        <div class="box">
          <div class="report-header">
            <h4>Top 10 Services</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=topServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=topServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartTopServices"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Service Demand Trend</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=serviceTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=serviceTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartServiceTrend"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Service Type Distribution</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=serviceTypeDistribution&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=serviceTypeDistribution&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartServiceTypeDist"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Demand by Weekday</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=weekdayDemand&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=weekdayDemand&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartWeekdayDemand"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Seasonal Demand</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=seasonalDemand&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=seasonalDemand&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartSeasonalDemand"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Turnaround Time by Branch</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=turnaroundByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=turnaroundByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartTurnaroundByBranch"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Repeat Customer Frequency</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=repeatCustomerFrequency&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=repeatCustomerFrequency&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartRepeatCustomerFrequency"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Most Rebooked Services</h4>
            <div class="report-downloads">
              <a href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mostRebookedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf" title="Download as PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
              <a href="<?= $B ?>/admin/admin-viewreports/export?key=mostRebookedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv" title="Download as CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            </div>
          </div>
          <div class="chart-box"><canvas id="chartMostRebookedServices"></canvas></div>
        </div>
      </div>
    </div>

    <div class="tab-content" id="tab-revenue" style="display:none;">
      <div class="section-title">Financial Analytics</div>
      <div class="grid2">
        <div class="box">
          <div class="report-header">
            <h4>Revenue Trend</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=revenueTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=revenueTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartRevenueTrend"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Cost Trend</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=costTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=costTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartCostTrend"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Profit Trend</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=profitTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=profitTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartProfitTrend"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Revenue by Branch</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=revenueByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=revenueByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartRevenueByBranch"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Revenue by Service Type</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=revenueByServiceType&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=revenueByServiceType&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartRevenueByServiceType"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Unpaid Invoice Aging</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=unpaidInvoiceAging&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=unpaidInvoiceAging&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartUnpaidInvoiceAging"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Payment Method Breakdown</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=paymentMethodBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=paymentMethodBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartPaymentMethodBreakdown"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Payment Status Breakdown</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=paymentStatusBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=paymentStatusBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartPaymentStatusBreakdown"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Branch Payment Collection Performance</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchPaymentCollection&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchPaymentCollection&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchPaymentCollection"></canvas></div>
        </div>
      </div>
    </div>

    <div class="tab-content" id="tab-appointments" style="display:none;">
      <div class="section-title">Appointment & Operations Analytics</div>
      <div class="grid2">
        <div class="box">
          <div class="report-header">
            <h4>Appointment Statuses</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=appointmentStatusCounts&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=appointmentStatusCounts&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartApptStatus"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Appointments by Hour</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=appointmentsByHour&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=appointmentsByHour&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartApptByHour"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Booking Trend</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=appointmentsTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=appointmentsTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartApptTrend"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Cancellation Trend</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=cancellationTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=cancellationTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartCancellationTrend"></canvas></div>
        </div>
      </div>
    </div>

    <div class="tab-content" id="tab-branches" style="display:none;">
      <div class="section-title">Branch Analytics</div>
      <div class="grid2">
        <div class="box">
          <div class="report-header">
            <h4>Completed Services</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchCompletedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchCompletedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchCompleted"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Average Rating</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchAvgRating&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchAvgRating&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchRating"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Capacity Utilization</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchCapacityUtilization&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchCapacityUtilization&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchCapacityUtilization"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Staffing vs Workload</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchStaffingVsWorkload&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchStaffingVsWorkload&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchStaffingVsWorkload"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Service Coverage Matrix</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchServiceCoverageMatrix&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchServiceCoverageMatrix&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchServiceCoverage"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Complaint Rate</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchComplaintRate&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchComplaintRate&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchComplaintRate"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Approval Rejection Rate</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchApprovalRejectionRate&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchApprovalRejectionRate&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchApprovalRejectionRate"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Quality Score</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchQualityScore&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchQualityScore&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchQualityScore"></canvas></div>
        </div>
        <div class="box">
          <div class="report-header">
            <h4>Underperforming Alerts</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=underperformingBranches&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=underperformingBranches&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartUnderperformingBranches"></canvas></div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <div class="tab-content" id="tab-staff" style="display:none;">
    <div class="section-title">Staff Analytics</div>
    <div class="grid2">
      <div class="box">
        <div class="report-header">
          <h4>Jobs per Mechanic</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=jobsPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=jobsPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartJobsPerMechanic"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Services Submitted by Managers</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=servicesSubmittedByManagers&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=servicesSubmittedByManagers&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartSubmittedByManagers"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Manager Approval Decisions</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=managerApprovalDecisions&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=managerApprovalDecisions&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartManagerApprovalDecisions"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Mechanic Quality Outcomes</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mechanicQualityOutcomes&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=mechanicQualityOutcomes&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartMechanicQualityOutcomes"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Staff Complaint Association</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=staffComplaintAssociation&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=staffComplaintAssociation&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartStaffComplaintAssociation"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Avg Jobs per Day per Mechanic</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=avgJobsPerDayPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=avgJobsPerDayPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartAvgJobsPerDayPerMechanic"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Delayed Work Orders by Mechanic</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=delayedWorkOrdersByMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=delayedWorkOrdersByMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartDelayedWorkOrdersByMechanic"></canvas></div>
      </div>
    </div>
  </div>

  <div class="tab-content" id="tab-feedback" style="display:none;">
    <div class="section-title">Feedback Analytics</div>
    <div class="grid2">
      <div class="box">
        <div class="report-header">
          <h4>Rating Distribution</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=ratingDistribution&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=ratingDistribution&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartRatingDist"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Feedback Trend</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=feedbackTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=feedbackTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartFeedbackTrend"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Lowest Rated Services</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=lowestRated&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=lowestRated&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartLowestRated"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Branch-wise Average Rating</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchRatingTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=branchRatingTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartBranchRatingTrend"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Rating by Service Type</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=ratingByServiceType&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=ratingByServiceType&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartRatingByServiceType"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Most Praised Services</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mostPraisedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=mostPraisedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartMostPraisedServices"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Repeat Negative Feedback Customers</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=repeatNegativeCustomers&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=repeatNegativeCustomers&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartRepeatNegativeCustomers"></canvas></div>
      </div>
    </div>
  </div>

  <div class="tab-content" id="tab-approval" style="display:none;">
    <div class="section-title">Approval Analytics</div>
    <div class="grid2">
      <div class="box">
        <div class="report-header">
          <h4>Service Approval Status</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=approvalStatusCounts&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=approvalStatusCounts&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartApprovalStatus"></canvas></div>
      </div>
    </div>
  </div>

  <div class="tab-content" id="tab-complaints" style="display:none;">
    <div class="section-title">Complaint Analytics</div>
    <div class="grid2">
      <div class="box">
        <div class="report-header">
          <h4>Complaint Trend</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=complaintTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=complaintTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartComplaintTrend"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Resolution Time Trend</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=complaintResolutionTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=complaintResolutionTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartComplaintResolutionTrend"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Closure Rate by Branch</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=complaintClosureRateByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=complaintClosureRateByBranch&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartComplaintClosureRate"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Priority Analysis</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=complaintPriorityAnalysis&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=complaintPriorityAnalysis&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartComplaintPriorityAnalysis"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Most Complained Services</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mostComplainedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=mostComplainedServices&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartMostComplainedServices"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Most Complained Branches</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mostComplainedBranches&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=mostComplainedBranches&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartMostComplainedBranches"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>Most Complained Staff</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=mostComplainedStaff&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=mostComplainedStaff&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartMostComplainedStaff"></canvas></div>
      </div>
      <div class="box">
        <div class="report-header">
          <h4>SLA Breach Trend</h4>
          <div class="report-downloads"><a
              href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=slaBreachTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
              href="<?= $B ?>/admin/admin-viewreports/export?key=slaBreachTrend&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
              class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
        </div>
        <div class="chart-box"><canvas id="chartSlaBreachTrend"></canvas></div>
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
      branch_id: "<?= (string) $branchId ?>",
      group: "<?= $group ?>"
    };
    window.REPORTS_BASE = "<?= $B ?>";
  </script>

  <script src="<?= $B ?>/public/assets/js/admin/reports/script.js"></script>
</body>

</html>