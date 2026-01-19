<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-profile.css">
</head>
<body>

<div class="sidebar">
     <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
      <h2>AUTONEXUS</h2>
      <a href="<?= $base ?>/supervisor/dashboard" class="nav">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="<?= $base ?>/supervisor/workorders">
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="<?= $base ?>/supervisor/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned 
    </a>
    <a href="<?= $base ?>/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="<?= $base ?>/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
     </a>
      <a href="<?= $base ?>/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
     </a>
      <a href="<?= $base ?>/supervisor/reports">
       <img src="/autonexus/public/assets/img/Inspection.png"/>Report
     </a>
    </div>

    <main class="main-content">
<div class="container">
  <h2>Edit Profile</h2>

  <form method="POST" action="<?= $base ?>/supervisor/profile/update" class="profile-form">

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
      <label>Username</label>
      <input type="text" name="username"
             value="<?= htmlspecialchars($user['username'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email"
             value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone"
             value="<?= htmlspecialchars($user['phone']) ?>" required>
    </div>

    <div class="form-group">
      <label>Alternate Phone</label>
      <input type="text" name="alt_phone"
             value="<?= htmlspecialchars($user['alt_phone'] ?? '') ?>">
    </div>

    <div class="form-group full">
      <label>Street Address</label>
      <input type="text" name="street_address"
             value="<?= htmlspecialchars($user['street_address'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>City</label>
      <input type="text" name="city"
             value="<?= htmlspecialchars($user['city'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>State</label>
      <input type="text" name="state"
             value="<?= htmlspecialchars($user['state'] ?? '') ?>">
    </div>

    <div class="form-group full">
      <label>New Password (optional)</label>
      <input type="password" name="password">
    </div>
  </div>

  <div class="form-actions">
    <button type="submit">Update Profile</button>
    <a href="<?= $base ?>/supervisor/dashboard" class="cancel-btn">Cancel</a>
  </div>

</form>

</div>

</main>
</body>
</html>
