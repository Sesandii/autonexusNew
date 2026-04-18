<?php
/** @var array $row */
/** @var array $managers */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
$curManagerId = (int) ($row['manager_id'] ?? 0);

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Branch <?= e($row['branch_code'] ?? '') ?></title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/admin/admin-viewbranches/branches.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="page-header">
      <h1>Edit Branch</h1>
      <p>Update the details for branch <?= e($row['branch_code'] ?? '') ?></p>
    </header>

    <form method="post" action="<?= e($base . '/admin/branches/' . rawurlencode((string) $row['branch_code'])) ?>"
      class="form-card">
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
                $id = (int) ($m['manager_id'] ?? 0);
                $code = (string) ($m['manager_code'] ?? '');
                $name = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                $label = $code ? "$code — $name" : $name;
                ?>
                <option value="<?= e((string) $id) ?>" <?= $id === $curManagerId ? 'selected' : '' ?>>
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
            <input class="input" type="date" name="created_at"
              value="<?= e(substr((string) ($row['created_at'] ?? ''), 0, 10)) ?>">
          </div>

          <div class="field">
            <label class="label">Capacity</label>
            <input class="input" type="number" name="capacity" value="<?= e((string) ($row['capacity'] ?? 0)) ?>"
              min="0">
          </div>

          <div class="field">
            <label class="label">Staff Count</label>
            <input class="input" type="number" name="staff" value="<?= e((string) ($row['staff_count'] ?? 0)) ?>"
              min="0">
          </div>

          <div class="field full">
            <label class="label">Address / Working Hours</label>
            <input class="input" name="address_line" value="<?= e($row['address_line'] ?? '') ?>"
              placeholder="e.g. Mon–Fri 08:00–17:00 or address">
          </div>

          <div class="field full">
            <label class="label">Notes</label>
            <textarea class="input" name="notes" rows="4"><?= e($row['notes'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="actions">
          <a href="<?= e($base . '/admin/branches/' . rawurlencode((string) $row['branch_code'])) ?>"
            class="btn-secondary">
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