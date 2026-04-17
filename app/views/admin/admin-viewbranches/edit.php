<?php
/** @var array $row */
/** @var array $managers */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
$curManagerId = (int)($row['manager_id'] ?? 0);

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Branch <?= e($row['branch_code'] ?? '') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Inter', Arial, sans-serif;
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

    .form-card {
      max-width: 1100px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
      overflow: hidden;
    }

    .form-card-header {
      padding: 20px 24px;
      border-bottom: 1px solid #e5e7eb;
    }

    .form-card-header h2 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
      color: #0f172a;
    }

    .form-card-body {
      padding: 22px 24px 24px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px 20px;
    }

    .field.full {
      grid-column: 1 / -1;
    }

    .label {
      display: block;
      margin-bottom: 8px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #94a3b8;
    }

    .input,
    select,
    textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      background: #fff;
      color: #111827;
      font-size: 14px;
      font-family: inherit;
      transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-color: #fb923c;
      box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.16);
    }

    .input[readonly] {
      background: #f8fafc;
      color: #64748b;
    }

    textarea {
      resize: vertical;
      min-height: 110px;
    }

    .actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 24px;
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
      cursor: pointer;
    }

    .btn-primary {
      background: #d94801;
      color: #fff;
      border: 1px solid #d94801;
    }

    .btn-primary:hover {
      background: #c2410c;
      border-color: #c2410c;
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
      .form-grid {
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

      .form-card-header,
      .form-card-body {
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
      <h1>Edit Branch</h1>
      <p>Update the details for branch <?= e($row['branch_code'] ?? '') ?></p>
    </header>

    <form method="post" action="<?= e($base . '/admin/branches/' . rawurlencode((string)$row['branch_code'])) ?>" class="form-card">
      <div class="form-card-header">
        <h2>Branch Information</h2>
      </div>

      <div class="form-card-body">
        <div class="form-grid">
          <div class="field">
            <label class="label">Branch Code</label>
            <input class="input" name="code" value="<?= e($row['branch_code'] ?? '') ?>" readonly>
          </div>

          <div class="field">
            <label class="label">Status</label>
            <?php $st = $row['status'] ?? 'active'; ?>
            <select name="status" class="input">
              <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>

          <div class="field">
            <label class="label">Branch Name</label>
            <input class="input" name="name" value="<?= e($row['name'] ?? '') ?>" required>
          </div>

          <div class="field">
            <label class="label">City</label>
            <input class="input" name="city" value="<?= e($row['city'] ?? '') ?>" required>
          </div>

          <div class="field">
            <label class="label">Manager</label>
            <select class="input" name="manager">
              <option value="">— None —</option>
              <?php foreach (($managers ?? []) as $m): ?>
                <?php
                  $id   = (int)($m['manager_id'] ?? 0);
                  $code = (string)($m['manager_code'] ?? '');
                  $name = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                  $label = $code ? "$code — $name" : $name;
                ?>
                <option value="<?= e((string)$id) ?>" <?= $id === $curManagerId ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label class="label">Phone</label>
            <input class="input" name="phone" value="<?= e($row['phone'] ?? '') ?>">
          </div>

          <div class="field">
            <label class="label">Email</label>
            <input class="input" type="email" name="email" value="<?= e($row['email'] ?? '') ?>">
          </div>

          <div class="field">
            <label class="label">Created At</label>
            <input class="input" type="date" name="created_at" value="<?= e(substr((string)($row['created_at'] ?? ''), 0, 10)) ?>">
          </div>

          <div class="field">
            <label class="label">Capacity</label>
            <input class="input" type="number" name="capacity" value="<?= e((string)($row['capacity'] ?? 0)) ?>" min="0">
          </div>

          <div class="field">
            <label class="label">Staff Count</label>
            <input class="input" type="number" name="staff" value="<?= e((string)($row['staff_count'] ?? 0)) ?>" min="0">
          </div>

          <div class="field full">
            <label class="label">Address / Working Hours</label>
            <input class="input" name="address_line" value="<?= e($row['address_line'] ?? '') ?>" placeholder="e.g. Mon–Fri 08:00–17:00 or address">
          </div>

          <div class="field full">
            <label class="label">Notes</label>
            <textarea class="input" name="notes" rows="4"><?= e($row['notes'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="actions">
          <a href="<?= e($base . '/admin/branches/' . rawurlencode((string)$row['branch_code'])) ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Changes</span>
          </button>
        </div>
      </div>
    </form>
  </main>
</body>
</html>