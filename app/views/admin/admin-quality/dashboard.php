<?php $B = rtrim(BASE_URL, '/');
$current = $current ?? 'qc-dashboard'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Quality Dashboard') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content {
      margin-left: 260px;
      padding: 30px;
      background: #f8fafc;
      min-height: 100vh
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 20px
    }

    .card,
    .mini {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 18px
    }

    .mini .label {
      font-size: 12px;
      color: #64748b
    }

    .mini .value {
      font-size: 24px;
      font-weight: 800;
      margin-top: 6px
    }

    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px
    }

    .list {
      list-style: none;
      padding: 0;
      margin: 0
    }

    .list li {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 10px 0;
      border-bottom: 1px solid #e2e8f0
    }

    .list li:last-child {
      border-bottom: none
    }

    .section-title {
      margin: 0 0 10px;
      font-size: 18px;
      font-weight: 800
    }

    .muted {
      color: #64748b
    }

    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700
    }

    .badge.ok {
      background: #dcfce7;
      color: #166534
    }

    .badge.warn {
      background: #fef3c7;
      color: #92400e
    }

    .badge.bad {
      background: #fee2e2;
      color: #991b1b
    }

    .compact {
      font-size: 13px
    }

    @media (max-width: 1200px) {
      .cards {
        grid-template-columns: repeat(2, minmax(0, 1fr))
      }

      .grid {
        grid-template-columns: 1fr
      }

      .main-content {
        margin-left: 0
      }
    }
  </style>
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <h1>Quality Dashboard</h1>
    <p class="muted">Deeper quality-control analytics using existing reports, checklists, final reports, and photo
      evidence.</p>

    <section class="cards">
      <div class="mini">
        <div class="label">Inspection Reports</div>
        <div class="value"><?= (int) ($summary['reports_total'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Draft Reports</div>
        <div class="value"><?= (int) ($summary['reports_draft'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Submitted Reports</div>
        <div class="value"><?= (int) ($summary['reports_submitted'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Final Reports</div>
        <div class="value"><?= (int) ($summary['final_reports_total'] ?? 0) ?></div>
      </div>

      <div class="mini">
        <div class="label">Checklist Items</div>
        <div class="value"><?= (int) ($summary['checklists_total'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Uploaded Photos</div>
        <div class="value"><?= (int) ($summary['photos_total'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Low Rated Cases</div>
        <div class="value"><?= (int) ($summary['low_rated_total'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Checklist Failures</div>
        <div class="value"><?= (int) ($summary['failed_checklist_total'] ?? 0) ?></div>
      </div>
    </section>

    <section class="cards u-mt-0">
      <div class="mini">
        <div class="label">Reinspection Queue</div>
        <div class="value"><?= (int) ($summary['reinspection_queue_total'] ?? 0) ?></div>
      </div>
      <div class="mini">
        <div class="label">Checklist Compliance</div>
        <div class="value"><?= number_format((float) $checklistCompliance, 1) ?>%</div>
      </div>
      <div class="mini">
        <div class="label">Test Drive Completion</div>
        <div class="value"><?= number_format((float) $testDriveRate, 1) ?>%</div>
      </div>
      <div class="mini">
        <div class="label">Concerns Addressed</div>
        <div class="value"><?= number_format((float) $concernRate, 1) ?>%</div>
      </div>
    </section>

    <section class="grid">
      <div class="card">
        <h2 class="section-title">Quality Ratings Distribution</h2>
        <ul class="list">
          <?php if (!empty($ratings)): ?>
            <?php foreach ($ratings as $r): ?>
              <li>
                <span>Rating <?= htmlspecialchars((string) $r['label']) ?></span>
                <strong><?= (int) $r['total'] ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Inspection Status Distribution</h2>
        <ul class="list">
          <?php if (!empty($statuses)): ?>
            <?php foreach ($statuses as $r): ?>
              <li>
                <span><?= htmlspecialchars(ucfirst((string) $r['label'])) ?></span>
                <strong><?= (int) $r['total'] ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Reports by Branch</h2>
        <ul class="list">
          <?php if (!empty($branches)): ?>
            <?php foreach ($branches as $b): ?>
              <li>
                <span><?= htmlspecialchars($b['label']) ?></span>
                <strong><?= (int) $b['total'] ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Branch-wise QC Score</h2>
        <ul class="list">
          <?php if (!empty($branchScores)): ?>
            <?php foreach ($branchScores as $b): ?>
              <li>
                <span><?= htmlspecialchars($b['label']) ?></span>
                <strong><?= number_format((float) $b['total'], 2) ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0.00</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Mechanic-wise QC Outcomes</h2>
        <ul class="list">
          <?php if (!empty($mechanics)): ?>
            <?php foreach ($mechanics as $m): ?>
              <li>
                <span><?= htmlspecialchars($m['label']) ?></span>
                <strong><?= number_format((float) $m['total'], 2) ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0.00</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Supervisor-wise QC Outcomes</h2>
        <ul class="list">
          <?php if (!empty($supervisors)): ?>
            <?php foreach ($supervisors as $s): ?>
              <li>
                <span><?= htmlspecialchars($s['label']) ?></span>
                <strong><?= number_format((float) $s['total'], 2) ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0.00</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Photo Evidence by Branch</h2>
        <ul class="list">
          <?php if (!empty($photoByBranch)): ?>
            <?php foreach ($photoByBranch as $p): ?>
              <li>
                <span><?= htmlspecialchars($p['label']) ?></span>
                <strong><?= (int) $p['total'] ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Final Reports by Branch</h2>
        <ul class="list">
          <?php if (!empty($finalByBranch)): ?>
            <?php foreach ($finalByBranch as $f): ?>
              <li>
                <span><?= htmlspecialchars($f['label']) ?></span>
                <strong><?= (int) $f['total'] ?></strong>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No data</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>
    </section>

    <section class="grid u-mt-18">
      <div class="card">
        <h2 class="section-title">Failed Quality Cases</h2>
        <ul class="list compact">
          <?php if (!empty($failedCases)): ?>
            <?php foreach ($failedCases as $r): ?>
              <li class="u-display-block">
                <div class="u-flex-between-gap-12">
                  <strong>Report #<?= (int) $r['report_id'] ?></strong>
                  <?php
                  $bad = ((int) ($r['quality_rating'] ?? 0) <= 2)
                    || ((int) ($r['checklist_verified'] ?? 0) === 0)
                    || ((int) ($r['test_driven'] ?? 0) === 0)
                    || ((int) ($r['concerns_addressed'] ?? 0) === 0);
                  ?>
                  <span class="badge <?= $bad ? 'bad' : 'ok' ?>"><?= $bad ? 'Needs Review' : 'OK' ?></span>
                </div>
                <div class="muted"><?= htmlspecialchars($r['customer_name'] ?? '—') ?> •
                  <?= htmlspecialchars($r['branch_name'] ?? '—') ?> • <?= htmlspecialchars($r['service_name'] ?? '—') ?>
                </div>
                <div class="muted">Rating: <?= htmlspecialchars((string) ($r['quality_rating'] ?? '0')) ?> | Checklist:
                  <?= (int) ($r['checklist_verified'] ?? 0) ? 'Yes' : 'No' ?> | Test Drive:
                  <?= (int) ($r['test_driven'] ?? 0) ? 'Yes' : 'No' ?> | Concerns:
                  <?= (int) ($r['concerns_addressed'] ?? 0) ? 'Yes' : 'No' ?></div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No failed cases</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="card">
        <h2 class="section-title">Reinspection / Follow-up Queue</h2>
        <ul class="list compact">
          <?php if (!empty($reinspectionQueue)): ?>
            <?php foreach ($reinspectionQueue as $r): ?>
              <li class="u-display-block">
                <div class="u-flex-between-gap-12">
                  <strong>Report #<?= (int) $r['report_id'] ?></strong>
                  <?php
                  $severity = ((int) ($r['quality_rating'] ?? 0) <= 2) ? 'bad' : 'warn';
                  ?>
                  <span
                    class="badge <?= $severity ?>"><?= htmlspecialchars(ucfirst((string) ($r['status'] ?? 'draft'))) ?></span>
                </div>
                <div class="muted"><?= htmlspecialchars($r['customer_name'] ?? '—') ?> •
                  <?= htmlspecialchars($r['branch_name'] ?? '—') ?></div>
                <div class="muted">Rating: <?= htmlspecialchars((string) ($r['quality_rating'] ?? '0')) ?> | Checklist:
                  <?= (int) ($r['checklist_verified'] ?? 0) ? 'Yes' : 'No' ?> | Test Drive:
                  <?= (int) ($r['test_driven'] ?? 0) ? 'Yes' : 'No' ?> | Concerns:
                  <?= (int) ($r['concerns_addressed'] ?? 0) ? 'Yes' : 'No' ?></div>
                <div class="muted">Updated: <?= htmlspecialchars($r['updated_at'] ?? '—') ?></div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No follow-up queue</span><strong>0</strong></li>
          <?php endif; ?>
        </ul>
      </div>
    </section>
  </main>
</body>

</html>