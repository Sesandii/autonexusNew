<?php /* Admin view: renders admin-viewmechanics/create page. */ ?>
<?php
$current = $current ?? 'mechanics';
$B = rtrim(BASE_URL, '/');
$old = $old ?? [];
$errors = $errors ?? [];

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
  <title>Add Mechanic</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Add Mechanic</h1>
          <p>Create a new mechanic profile with branch assignment and specialization details.</p>
        </div>
        <a href="<?= e($B . '/admin/mechanics') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Mechanics</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post" action="<?= e($B . '/admin/mechanics') ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-tools"></i>
            <h2>Mechanic Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label">First Name</label>
                <input class="input" name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Last Name</label>
                <input class="input" name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?= e($old['email'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Phone</label>
                <input class="input" name="phone" value="<?= e($old['phone'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Assigned Branch</label>
                <select name="branch_id" class="input" required>
                  <option value="">-- Select a branch --</option>
                  <?php foreach (($branches ?? []) as $b): ?>
                    <option value="<?= (int) $b['branch_id'] ?>" <?= (($old['branch_id'] ?? '') == $b['branch_id']) ? 'selected' : '' ?>>
                      <?= e(($b['branch_code'] ? $b['branch_code'] . ' • ' : '') . ($b['name'] ?? 'Branch')) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label">Specialization</label>
                <input class="input" name="specialization" value="<?= e($old['specialization'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Experience (years)</label>
                <input class="input" type="number" min="0" name="experience_years"
                  value="<?= e($old['experience_years'] ?? '0') ?>">
              </div>

              <div class="field">
                <label class="label">Mechanic Status</label>
                <select name="mech_status" class="input">
                  <option value="active" <?= (($old['mech_status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active
                  </option>
                  <option value="inactive" <?= (($old['mech_status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive
                  </option>
                </select>
              </div>

              <div class="field">
                <label class="label">User Status</label>
                <select name="user_status" class="input">
                  <option value="active" <?= (($old['user_status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active
                  </option>
                  <option value="inactive" <?= (($old['user_status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive
                  </option>
                  <option value="pending" <?= (($old['user_status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending
                  </option>
                </select>
              </div>

              <div class="field">
                <label class="label">Password</label>
                <input class="input" type="password" name="password" placeholder="default: Mechanic@123">
                <div class="hint">Default password is Mechanic@123.</div>
              </div>

              <div class="field">
                <label class="label">Mechanic Code</label>
                <input class="input" name="mechanic_code" value="<?= e($old['mechanic_code'] ?? '') ?>"
                  placeholder="Auto-generated if empty">
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/mechanics') ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Mechanic</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>