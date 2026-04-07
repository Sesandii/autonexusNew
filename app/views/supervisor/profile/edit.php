<?php $base = rtrim(BASE_URL,'/'); ?>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<?php
// Match what the controller saved: $_SESSION['message']
$message = $_SESSION['message'] ?? null;

if ($message): ?>
    <div class="toast-container">
        <div class="toast-message <?= htmlspecialchars($message['type']) ?>">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    </div>
    <?php unset($_SESSION['message']); // Clear it so it doesn't repeat ?>
<?php endif; ?>

<main class="container">
  <div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Edit profile <span class="sep"></span> 
  </div>

  <div class="profile-header-card">
  <div class="profile-info-left">
    <div class="avatar-circle">
      <i class="fas fa-user"></i>
    </div>
    <div class="profile-summary">
      <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
      <div class="badge-row">
        <span class="role-badge">
          <i class="fas fa-shield-alt"></i> <?= htmlspecialchars($user['role']) ?>
        </span>
        <span class="email-info">
          <i class="far fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
        </span>
      </div>
    </div>
  </div>
</div>
    
  <div class="profile-details-card">
    <div class="card-header">
      <h3><i class="fas fa-id-card"></i> Profile Details</h3>
      <p>Update your contact and address information.</p>
    </div>

    <form method="POST" action="<?= $base ?>/supervisor/profile/update" class="profile-form">
      <div class="form-grid">
        <div class="form-group">
          <label>First Name</label>
          <div class="input-with-icon">
            <i class="far fa-user"></i>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Last Name</label>
          <div class="input-with-icon">
            <i class="far fa-user"></i>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
          </div>
        </div>

        <div class="form-group">
  <label>Email</label>
  <div class="input-with-icon">
    <i class="far fa-envelope"></i>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly class="readonly-style">
  </div>
</div>
        <div class="form-group">
          <label>Phone</label>
          <div class="input-with-icon">
            <i class="fas fa-phone-alt"></i>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Alt Phone</label>
          <div class="input-with-icon">
            <i class="fas fa-phone-alt"></i>
            <input type="text" name="alt_phone" value="<?= htmlspecialchars($user['alt_phone']) ?>" placeholder="Optional">
          </div>
        </div>

        <div class="form-group">
          <label>City</label>
          <div class="input-with-icon">
          <i class="fa-regular fa-building"></i>
            <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Street Address</label>
          <div class="input-with-icon">
          <i class="fa-regular fa-map"></i>
            <input type="text" name="street_address" value="<?= htmlspecialchars($user['street_address']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Province</label>
          <div class="input-with-icon">
          <i class="fa-regular fa-compass"></i>
            <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
          </div>
        </div>
        

        <input type="hidden" name="role" value="<?= htmlspecialchars($user['role']) ?>">
        <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">

        </div>

      <div class="form-actions">
        <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save Changes</button>
      </div>
      
</form>

</div>

</main>


</body>
</html>
