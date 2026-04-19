<?php /* Admin view: renders admin-viewbranches/create page. */ ?>
<?php
/** @var string $base */
/** @var array $managers */
/** @var string $nextCode */
$errors = $errors ?? [];
$old = $old ?? [];
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
$nextCode = $nextCode ?? 'BR001';

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
  <title>Add New Branch</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Add New Branch</h1>
          <p>Create a new service branch with contact, assignment, and capacity details.</p>
        </div>
        <a href="<?= e($base . '/admin/branches') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Branches</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post" action="<?= e($base . '/admin/branches') ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-building"></i>
            <h2>Branch Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label">Branch Code</label>
                <input class="input" name="code" value="<?= e($old['branch_code'] ?? $nextCode) ?>" readonly>
              </div>

              <div class="field">
                <label class="label">Status</label>
                <select class="input" name="status">
                  <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active
                  </option>
                  <option value="inactive" <?= (($old['status'] ?? 'active') === 'inactive') ? 'selected' : '' ?>>Inactive
                  </option>
                </select>
              </div>

              <div class="field">
                <label class="label">Branch Name</label>
                <input class="input" name="name" placeholder="Branch name" value="<?= e($old['name'] ?? '') ?>"
                  required>
              </div>

              <div class="field">
                <label class="label">City / Location</label>
                <input class="input" name="city" placeholder="City" value="<?= e($old['city'] ?? '') ?>" required>
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
                    <option value="<?= e((string) $id) ?>" <?= ((string) ($old['manager_id'] ?? '') === (string) $id) ? 'selected' : '' ?>>
                      <?= e($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label">Phone</label>
                <input class="input" type="tel" inputmode="numeric" name="phone" pattern="^0[0-9]{9}$" maxlength="10"
                  autocomplete="tel" placeholder="0712345678" value="<?= e($old['phone'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" placeholder="example@autonexus.com"
                  value="<?= e($old['email'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Created At</label>
                <input class="input" type="date" name="created_at"
                  value="<?= e(substr((string) ($old['created_at'] ?? date('Y-m-d')), 0, 10)) ?>">
              </div>

              <div class="field">
                <label class="label">Capacity</label>
                <input class="input" type="number" name="capacity" min="0"
                  value="<?= e((string) ($old['capacity'] ?? 0)) ?>">
              </div>

              <div class="field">
                <label class="label">Staff Count</label>
                <input class="input" type="number" name="staff" min="0"
                  value="<?= e((string) ($old['staff_count'] ?? 0)) ?>">
              </div>

              <div class="field full">
                <label class="label">Address</label>
                <input class="input" name="address_line" placeholder="Branch address"
                  value="<?= e($old['address_line'] ?? '') ?>">
              </div>

              <div class="field full">
                <label class="label">Notes</label>
                <textarea class="input" name="notes" rows="4"
                  placeholder="Optional notes..."><?= e($old['notes'] ?? '') ?></textarea>
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a href="<?= e($base . '/admin/branches') ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button type="submit" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Create Branch</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>