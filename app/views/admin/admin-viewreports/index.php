<?php /* Admin view: renders admin-viewreports/index page. */ ?>
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
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-viewreportsindex.css?v=1">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <div class="main">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
      <h2 style="margin:0;">Reports & Analytics</h2>
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
            <h4>Payment Success</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=paymentStatusBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=paymentStatusBreakdown&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartPaymentStatusBreakdown"></canvas></div>
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
            <h4>Service Coverage Matrix</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=branchServiceCoverageMatrix&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=branchServiceCoverageMatrix&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartBranchServiceCoverage"></canvas></div>
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
            <h4>Avg Jobs per Day per Mechanic</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=avgJobsPerDayPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=avgJobsPerDayPerMechanic&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartAvgJobsPerDayPerMechanic"></canvas></div>
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
            <h4>Priority Analysis</h4>
            <div class="report-downloads"><a
                href="<?= $B ?>/admin/admin-viewreports/export-pdf?key=complaintPriorityAnalysis&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn pdf"><i class="fa-solid fa-file-pdf"></i> PDF</a><a
                href="<?= $B ?>/admin/admin-viewreports/export?key=complaintPriorityAnalysis&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&branch_id=<?= $branchId ?>&group=<?= $group ?>"
                class="download-btn csv"><i class="fa-solid fa-file-csv"></i> CSV</a></div>
          </div>
          <div class="chart-box"><canvas id="chartComplaintPriorityAnalysis"></canvas></div>
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