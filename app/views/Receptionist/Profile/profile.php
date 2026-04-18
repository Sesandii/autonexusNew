<?php
$base = rtrim(BASE_URL, '/');
$activePage = 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/receptionist/myprofile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class = "main">


  <!-- HEADER CARD -->
  <div class="profile-header-card">

    <div class="profile-info-left">

      <div class="avatar-circle">
        <i class="fas fa-user"></i>
      </div>

      <div class="profile-summary">
        <h1>
          <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
        </h1>

        <div class="badge-row">

          <span class="role-badge">
            <?= htmlspecialchars($user['role']) ?>
          </span>

          <span>
            <i class="far fa-envelope"></i>
            <?= htmlspecialchars($user['email']) ?>
          </span>

        </div>
      </div>

    </div>

  </div>

  <!-- PROFILE FORM CARD -->
  <form method="POST" action="<?= $base ?>/receptionist/profile/update" class="profile-form">
    <h2>Profile Settings</h2>

    <div class="form-grid">

      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="first_name"
               value="<?= htmlspecialchars($user['first_name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="last_name"
               value="<?= htmlspecialchars($user['last_name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email"
               value="<?= htmlspecialchars($user['email']) ?>"
               readonly>
      </div>

      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone"
               value="<?= htmlspecialchars($user['phone']) ?>" required>
      </div>

      <div class="form-group">
        <label>Alt Phone</label>
        <input type="text" name="alt_phone"
               value="<?= htmlspecialchars($user['alt_phone']) ?>">
      </div>

      <div class="form-group">
        <label>City</label>
        <input type="text" name="city"
               value="<?= htmlspecialchars($user['city']) ?>" required>
      </div>

      <div class="form-group full">
        <label>Street Address</label>
        <input type="text" name="street_address"
               value="<?= htmlspecialchars($user['street_address']) ?>" required>
      </div>

      <div class="form-group">
        <label>State</label>
        <input type="text" name="state"
               value="<?= htmlspecialchars($user['state']) ?>" required>
      </div>

    </div>

    <!-- ACTIONS -->
    <div class="form-actions">
      <button type="submit">Save Changes</button>
      <a href="<?= $base ?>/dashboard" class="cancel-btn">Cancel</a>
    </div>

  </form>

</div>

</body>
</html>