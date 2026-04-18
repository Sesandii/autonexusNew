<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$st = $row['status'] ?? 'active';
$statusClass = $st === 'inactive' ? 'status-pill inactive' : 'status-pill active';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch <?= e($row['branch_code'] ?? '') ?> • Details</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f3f4f6;
      color: #111827;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      overflow-y: auto;
    }

    .main-content {
      margin-left: 260px;
      min-height: 100vh;
      padding: 28px;
      background: #f3f4f6;
    }

    .page-header {
      margin-bottom: 22px;
    }

    .page-header h1 {
      margin: 0;
      font-size: 32px;
      line-height: 1.15;
      font-weight: 800;
      color: #0f172a;
    }

    .page-header p {
      margin: 8px 0 0;
      font-size: 15px;
      color: #64748b;
    }

    .details-card {
      max-width: 1080px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
      overflow: hidden;
    }

    .details-card-header {
      padding: 20px 24px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .details-card-header h2 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
      color: #0f172a;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 14px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid transparent;
    }

    .status-pill::before {
      content: "";
      width: 9px;
      height: 9px;
      border-radius: 50%;
      display: inline-block;
    }

    .status-pill.active {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #166534;
    }

    .status-pill.active::before {
      background: #22c55e;
    }

    .status-pill.inactive {
      background: #fee2e2;
      border-color: #fecaca;
      color: #991b1b;
    }

    .status-pill.inactive::before {
      background: #dc2626;
    }

    .details-body {
      padding: 22px 24px 24px;
    }

    .details-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px 20px;
    }

    .detail-item {
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 16px 18px;
    }

    .detail-item.full {
      grid-column: 1 / -1;
    }

    .detail-label {
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #94a3b8;
      margin-bottom: 8px;
    }

    .detail-value {
      font-size: 15px;
      font-weight: 600;
      color: #0f172a;
      line-height: 1.5;
      word-break: break-word;
    }

    .actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 22px;
      flex-wrap: wrap;
    }

    .btn-primary,
    .btn-secondary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 11px 16px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 700;
      transition: all .2s ease;
    }

    .btn-primary {
      background: #111827;
      color: #fff;
      border: 1px solid #111827;
    }

    .btn-primary:hover {
      background: #020617;
      border-color: #020617;
    }

    .btn-secondary {
      background: #fff;
      color: #374151;
      border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
      background: #f9fafb;
    }

    @media (max-width: 900px) {
      .details-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 16px;
      }

      .sidebar {
        position: static;
        width: 100%;
        height: auto;
      }

      .page-header h1 {
        font-size: 26px;
      }

      .details-card-header,
      .details-body {
        padding-left: 16px;
        padding-right: 16px;
      }
    }
  </style>
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="page-header">
      <h1>Branch Details</h1>
      <p>View complete information for branch <?= e($row['branch_code'] ?? '') ?></p>
    </header>

    <section class="details-card">
      <div class="details-card-header">
        <h2><?= e($row['name'] ?? '') ?></h2>
        <span class="<?= e($statusClass) ?>"><?= e(ucfirst((string) $st)) ?></span>
      </div>

      <div class="details-body">
        <div class="details-grid">
          <div class="detail-item">
            <div class="detail-label">Branch Code</div>
            <div class="detail-value"><?= e($row['branch_code'] ?? '') ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">City</div>
            <div class="detail-value"><?= e($row['city'] ?? '') ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Manager ID</div>
            <div class="detail-value"><?= e($row['manager_id'] ?? '') ?: 'Not assigned' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Phone</div>
            <div class="detail-value"><?= e($row['phone'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value"><?= e($row['email'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Created At</div>
            <div class="detail-value"><?= e($row['created_at'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Capacity</div>
            <div class="detail-value"><?= e($row['capacity'] ?? '') ?: '0' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Staff Count</div>
            <div class="detail-value"><?= e($row['staff_count'] ?? '') ?: '0' ?></div>
          </div>

          <div class="detail-item full">
            <div class="detail-label">Address / Working Hours</div>
            <div class="detail-value"><?= e($row['address_line'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item full">
            <div class="detail-label">Notes</div>
            <div class="detail-value"><?= nl2br(e($row['notes'] ?? '')) ?: '—' ?></div>
          </div>
        </div>

        <div class="actions">
          <a class="btn-secondary" href="<?= e($base . '/admin/branches') ?>">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to list</span>
          </a>

          <a class="btn-primary"
            href="<?= e($base . '/admin/branches/' . rawurlencode((string) $row['branch_code']) . '/edit') ?>">
            <i class="fa-solid fa-pen"></i>
            <span>Edit Branch</span>
          </a>
        </div>
      </div>
    </section>
  </main>
</body>

</html>