<?php /* Admin view: renders admin-viewbranches/edit page. */ ?>
<?php
/** @var array $row */
/** @var array $managers */
/** @var string $base */
$errors = $errors ?? [];
$old = $old ?? [];
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
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="page-header">
      <h1>Edit Branch</h1>
      <p>Update the details for branch <?= e($row['branch_code'] ?? '') ?></p>
    </header>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <div><?= e($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= e($base . '/admin/branches/' . rawurlencode((string) $row['branch_code'])) ?>"
      class="form-card">
      <div class="form-card-header">
        <h2>Branch Information</h2>
      </div>

      <div class="form-card-body">
        <div class="form-grid">
          <div class="field">
            <label class="label">Branch Code</label>
            <input class="input" name="code" value="<?= e($old['branch_code'] ?? $row['branch_code'] ?? '') ?>"
              readonly>
          </div>

          <div class="field">
            <label class="label">Status</label>
            <?php $st = $old['status'] ?? ($row['status'] ?? 'active'); ?>
            <select name="status" class="input">
              <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>

          <div class="field">
            <label class="label">Branch Name</label>
            <input class="input" name="name" value="<?= e($old['name'] ?? $row['name'] ?? '') ?>" required>
          </div>

          <div class="field">
            <label class="label">City</label>
            <input class="input" name="city" value="<?= e($old['city'] ?? $row['city'] ?? '') ?>" required>
          </div>

          <div class="field">
            <label class="label">Manager</label>
            <select class="input" name="manager" required>
              <option value="">Select a manager</option>
              <?php foreach (($managers ?? []) as $m): ?>
                <?php
                $id = (int) ($m['manager_id'] ?? 0);
                $code = (string) ($m['manager_code'] ?? '');
                $name = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                $label = $code ? "$code — $name" : $name;
                ?>
                <option value="<?= e((string) $id) ?>" <?= ((string) ($old['manager_id'] ?? $curManagerId) === (string) $id) ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label class="label">Phone</label>
            <input class="input" type="tel" inputmode="numeric" name="phone" pattern="^0[0-9]{9}$" maxlength="10"
              autocomplete="tel" value="<?= e($old['phone'] ?? $row['phone'] ?? '') ?>" placeholder="0712345678">
          </div>

          <div class="field">
            <label class="label">Email</label>
            <input class="input" type="email" name="email" value="<?= e($old['email'] ?? $row['email'] ?? '') ?>">
          </div>

          <div class="field">
            <label class="label">Created At</label>
            <input class="input" type="date" name="created_at"
              value="<?= e(substr((string) ($old['created_at'] ?? $row['created_at'] ?? ''), 0, 10)) ?>">
          </div>

          <div class="field">
            <label class="label">Capacity</label>
            <input class="input" type="number" name="capacity"
              value="<?= e((string) ($old['capacity'] ?? $row['capacity'] ?? 0)) ?>" min="0">
          </div>

          <div class="field">
            <label class="label">Staff Count</label>
            <input class="input" type="number" name="staff"
              value="<?= e((string) ($old['staff_count'] ?? $row['staff_count'] ?? 0)) ?>" min="0">
          </div>

          <div class="field full">
            <label class="label">Address</label>
            <input class="input" name="address_line"
              value="<?= e($old['address_line'] ?? $row['address_line'] ?? '') ?>" placeholder="Branch address">
          </div>

          <div class="field full">
            <label class="label">Notes</label>
            <textarea class="input" name="notes" rows="4"><?= e($old['notes'] ?? $row['notes'] ?? '') ?></textarea>
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