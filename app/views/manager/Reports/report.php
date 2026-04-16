<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/report.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<main class="main">
  <header>
    <h1>Report Generation</h1>
  </header>

  <!-- ─── STEP 1: Pick report type ─────────────────────────────── -->
  <?php if ($step === 1): ?>

  <section class="card report-types">
    <h3>Select Report Type</h3>
    <div class="report-options">

      <a href="<?= BASE_URL ?>/manager/reports?step=2&report_type=revenue" class="report-box">
        <span class="icon">📝</span>
        <h4>Revenue &amp; Sales Report</h4>
        <p>Summary of service revenue, invoice totals, and payment trends</p>
      </a>

      <a href="<?= BASE_URL ?>/manager/reports?step=2&report_type=pending_services" class="report-box">
        <span class="icon">📊</span>
        <h4>Pending &amp; Overdue Services</h4>
        <p>List of ongoing, delayed, and overdue service jobs</p>
      </a>

      <a href="<?= BASE_URL ?>/manager/reports?step=2&report_type=service_completion" class="report-box">
        <span class="icon">⏱️</span>
        <h4>Service Completion Times</h4>
        <p>Average time to complete different service types</p>
      </a>

    </div>
  </section>

  <!-- ─── STEP 2: Configure filters ────────────────────────────── -->
  <?php elseif ($step === 2): ?>

  <section class="card report-params">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/manager/reports">← Back</a>
      <span><?= htmlspecialchars(ucwords(str_replace('_', ' ', $reportType))) ?></span>
    </div>

    <h3>Report Parameters</h3>

    <form method="POST" action="<?= BASE_URL ?>/manager/reports">
      <!-- Pass report type through to POST -->
      <input type="hidden" name="report_type" value="<?= htmlspecialchars($reportType) ?>">

      <!-- Date range (always shown) -->
      <div class="filter-group">
        <label for="from_date">From</label>
        <input type="date" id="from_date" name="from_date"
               value="<?= date('Y-m-01') ?>" required>
      </div>

      <div class="filter-group">
        <label for="to_date">To</label>
        <input type="date" id="to_date" name="to_date"
               value="<?= date('Y-m-d') ?>" required>
      </div>

      <!-- Revenue-specific filters -->
      <?php if ($reportType === 'revenue'): ?>

        <div class="filter-group">
          <label>Metrics</label>
          <label class="checkbox">
            <input type="checkbox" name="metrics[]" value="total_revenue" checked>
            Total Revenue
          </label>
          <label class="checkbox">
            <input type="checkbox" name="metrics[]" value="invoice_count">
            Invoice Count
          </label>
        </div>

        <?php if (!empty($services)): ?>
        <div class="filter-group">
          <label for="service_type">Service Type</label>
          <select id="service_type" name="service_type">
            <option value="">All Services</option>
            <?php foreach ($services as $s): ?>
              <option value="<?= (int)$s['service_id'] ?>">
                <?= htmlspecialchars($s['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

      <!-- Pending services filters -->
      <?php elseif ($reportType === 'pending_services'): ?>

        <div class="filter-group">
          <label>Status</label>
          <label class="checkbox">
            <input type="checkbox" name="status[]" value="pending" checked>
            Pending
          </label>
          <label class="checkbox">
            <input type="checkbox" name="status[]" value="overdue" checked>
            Overdue
          </label>
        </div>

      <?php endif; ?>

      <div class="actions">
        <a href="<?= BASE_URL ?>/manager/reports" class="cancel">Cancel</a>
        <button type="submit" class="generate">📑 Generate Report</button>
      </div>
    </form>
  </section>

  <!-- ─── STEP 3: Results ───────────────────────────────────────── -->
  <?php elseif ($step === 3): ?>

  <section class="card report-results">

    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/manager/reports">← New Report</a>
    </div>

    <h3>
      <?= htmlspecialchars(ucwords(str_replace('_', ' ', $reportType))) ?>
      <?php if ($from && $to): ?>
        <span class="date-range">(<?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?>)</span>
      <?php endif; ?>
    </h3>

    <?php if (empty($rows)): ?>
      <p class="no-data">No data found for the selected period.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="report-table">
          <thead>
            <tr>
              <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <?php foreach ($row as $cell): ?>
                  <td><?= htmlspecialchars($cell ?? '—') ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </section>

  <?php endif; ?>

</main>

</body>
</html>