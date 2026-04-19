<?php 
$base = rtrim(BASE_URL, '/'); 

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$message = $_SESSION['message'] ?? null;
$errors = $_SESSION['errors'] ?? [];
if ($message) unset($_SESSION['message']);
if ($errors) unset($_SESSION['errors']);

$editMode = $editMode ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $editMode ? 'Edit' : 'My' ?> Profile - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/myprofile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

<?php if ($message): ?>
<div class="toast-container">
    <div class="toast-message <?= htmlspecialchars($message['type']) ?>">
        <?= htmlspecialchars($message['text']) ?>
    </div>
</div>
<?php endif; ?>

<div class="container">
  <div class="profile-header-card">
    <div class="profile-info-left">
        <div class="avatar-circle">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= $base ?>/public/uploads/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile">
            <?php else: ?>
                <i class="fas fa-user"></i>
            <?php endif; ?>
        </div>
        <div class="profile-summary">
            <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
            <div class="badge-row">
                <span class="role-badge">
                    <i class="fas fa-shield-alt"></i> <?= ucfirst(htmlspecialchars($user['role'])) ?>
                </span>
                <span class="email-info">
                    <i class="far fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="profile-info-right">
        <div class="branch-display">
            <small>Assigned Branch</small>
            <h2><?= htmlspecialchars($user['branch_name'] ?? 'Not Assigned') ?></h2>
            <div class="branch-id">ID: <?= htmlspecialchars($user['branch_code'] ?? 'N/A') ?></div>
        </div>
    </div>
  </div>

  <div class="profile-details-card">
    <div class="card-header">
      <div class="header-left">
        <h3>
          <?php if ($editMode): ?>
            <i class="fas fa-edit"></i> Edit Profile
          <?php else: ?>
            <i class="fas fa-id-card"></i> Profile Details
          <?php endif; ?>
        </h3>
        <p><?= $editMode ? 'Update your personal and contact information' : 'Your personal and contact information' ?></p>
      </div>
      <div class="header-right">
        <?php if ($editMode): ?>
          <a href="<?= $base ?>/manager/profile" class="cancel-btn">
            <i class="fas fa-times"></i> Cancel
          </a>
        <?php else: ?>
          <a href="<?= $base ?>/manager/profile?edit=true" class="edit-btn">
            <i class="fas fa-edit"></i> Edit Profile
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($editMode && !empty($errors)): ?>
    <div class="error-container">
      <?php foreach ($errors as $error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($editMode): ?>
    <form method="POST" action="<?= $base ?>/manager/profile/update" class="profile-form">
    <?php else: ?>
    <div class="profile-form">
    <?php endif; ?>

      <div class="form-grid">
        <?php if ($editMode): ?>
        <div class="form-group">
          <label>First Name <span class="required">*</span></label>
          <div class="input-with-icon">
            <i class="fas fa-user"></i>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Last Name <span class="required">*</span></label>
          <div class="input-with-icon">
            <i class="fas fa-user"></i>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
          </div>
        </div>
        <?php else: ?>
        <div class="form-group">
          <label>Full Name</label>
          <div class="input-with-icon">
            <i class="fas fa-user"></i>
            <input type="text" value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" readonly class="readonly-style">
          </div>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label>Username</label>
          <div class="input-with-icon">
            <i class="fas fa-at"></i>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly class="readonly-style">
          </div>
        </div>

        <div class="form-group">
          <label>Email</label>
          <div class="input-with-icon">
            <i class="far fa-envelope"></i>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly class="readonly-style">
          </div>
          <?php if ($editMode): ?>
          <small class="hint">Email cannot be changed</small>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Phone <?= $editMode ? '<span class="required">*</span>' : '' ?></label>
          <div class="input-with-icon">
            <i class="fas fa-phone-alt"></i>
            <?php if ($editMode): ?>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            <?php else: ?>
            <input type="text" value="<?= htmlspecialchars($user['phone'] ?: '—') ?>" readonly class="readonly-style">
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Alt Phone</label>
          <div class="input-with-icon">
            <i class="fas fa-phone-alt"></i>
            <?php if ($editMode): ?>
            <input type="text" name="alt_phone" value="<?= htmlspecialchars($user['alt_phone']) ?>" placeholder="Optional">
            <?php else: ?>
            <input type="text" value="<?= htmlspecialchars($user['alt_phone'] ?: '—') ?>" readonly class="readonly-style">
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group full-width">
          <label>Street Address <?= $editMode ? '<span class="required">*</span>' : '' ?></label>
          <div class="input-with-icon">
            <i class="fas fa-map-marker-alt"></i>
            <?php if ($editMode): ?>
            <input type="text" name="street_address" value="<?= htmlspecialchars($user['street_address']) ?>" required>
            <?php else: ?>
            <input type="text" value="<?= htmlspecialchars($user['street_address'] ?: '—') ?>" readonly class="readonly-style">
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label>City <?= $editMode ? '<span class="required">*</span>' : '' ?></label>
          <div class="input-with-icon">
            <i class="fas fa-city"></i>
            <?php if ($editMode): ?>
            <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
            <?php else: ?>
            <input type="text" value="<?= htmlspecialchars($user['city'] ?: '—') ?>" readonly class="readonly-style">
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label>State/Province <?= $editMode ? '<span class="required">*</span>' : '' ?></label>
          <div class="input-with-icon">
            <i class="fas fa-map"></i>
            <?php if ($editMode): ?>
            <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
            <?php else: ?>
            <input type="text" value="<?= htmlspecialchars($user['state'] ?: '—') ?>" readonly class="readonly-style">
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Status</label>
          <div class="input-with-icon">
            <i class="fas fa-circle"></i>
            <input type="text" value="<?= ucfirst(htmlspecialchars($user['status'] ?? 'active')) ?>" readonly class="readonly-style">
          </div>
        </div>

        <div class="form-group">
          <label>Member Since</label>
          <div class="input-with-icon">
            <i class="far fa-calendar-alt"></i>
            <input type="text" value="<?= date('M d, Y', strtotime($user['created_at'])) ?>" readonly class="readonly-style">
          </div>
        </div>
      </div>

      <?php if ($editMode): ?>
      <div class="form-actions">
        <button type="submit" class="save-btn">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
      <?php endif; ?>

    <?php if ($editMode): ?>
    </form>
    <?php else: ?>
    </div>
    <?php endif; ?>
  </div>
</div>

</div>
</body>
</html>