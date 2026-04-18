<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Team Member - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/addMember.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

  <!-- Header -->
  <div class="page-header">
    <div class="header-left">
      <a href="<?= BASE_URL ?>/manager/schedule" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Team Overview
      </a>
      <h1>Add Team Member</h1>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
      <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Search Bar -->
 <!-- <div class="search-section">
    <form method="GET" action="<?= BASE_URL ?>/manager/schedule/add-member" class="search-form">
      <div class="search-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" 
               name="search" 
               placeholder="Search by name, email, or phone..." 
               class="search-input"
               value="<?= htmlspecialchars($search ?? '') ?>">
      </div>
     <button type="submit" class="btn-primary">
        <i class="fas fa-search"></i> Search
      </button>
      <?php if (!empty($search)): ?>
        <a href="<?= BASE_URL ?>/manager/schedule/add-member" class="btn-secondary">
          <i class="fas fa-times"></i> Clear
        </a>
      <?php endif; ?>
    </form>
  </div>-->

  <!-- Staff Table -->
  <div class="table-container">
    <?php if (!empty($allStaff)): ?>
      <table class="staff-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Current Branch</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allStaff as $staff): ?>
            <?php
              $roleClass = match($staff['role']) {
                  'supervisor' => 'supervisor',
                  'mechanic' => 'mechanic',
                  'receptionist' => 'receptionist',
                  default => ''
              };
              
              $inMyTeam = $staff['in_my_team'] == 1;
              $currentBranch = $staff['current_branch'] ? 'Branch ' . $staff['current_branch'] : 'Unassigned';
            ?>
            <tr class="<?= $inMyTeam ? 'in-team' : '' ?>">
              <td>
                <div class="employee-name">
                  <div class="employee-avatar-small">
                    <?= strtoupper(substr($staff['first_name'], 0, 1) . substr($staff['last_name'], 0, 1)) ?>
                  </div>
                  <div>
                    <strong><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></strong>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge <?= $roleClass ?>"><?= ucfirst($staff['role']) ?></span>
              </td>
              <td><?= htmlspecialchars($staff['phone'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($staff['email'] ?? 'N/A') ?></td>
              <td>
                <span class="branch-badge <?= $staff['current_branch'] ? 'assigned' : 'unassigned' ?>">
                  <i class="fas <?= $staff['current_branch'] ? 'fa-building' : 'fa-circle' ?>"></i>
                  <?= $currentBranch ?>
                </span>
              </td>
              <td>
                <?php if ($inMyTeam): ?>
                  <span class="status-badge in-team-badge">
                    <i class="fas fa-check"></i> In Your Team
                  </span>
                <?php else: ?>
                  <span class="status-badge available-badge">
                    <i class="fas fa-user-plus"></i> Available
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($inMyTeam): ?>
                  <button class="btn-disabled" disabled>
                    <i class="fas fa-check"></i> Already in Team
                  </button>
                <?php else: ?>
                 <form method="POST" action="<?= BASE_URL ?>/manager/schedule/assign-to-branch" style="display: inline;">
  <input type="hidden" name="user_id" value="<?= $staff['user_id'] ?>">
  <input type="hidden" name="role" value="<?= $staff['role'] ?>">
  <button type="submit" class="btn-assign" onclick="return confirm('Add this employee to your team?')">
    <i class="fas fa-plus"></i> Add to Team
  </button>
</form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="no-results">
        <i class="fas fa-users-slash"></i>
        <h3>No staff found</h3>
        <p><?= !empty($search) ? 'No results for "' . htmlspecialchars($search) . '"' : 'No staff members available' ?></p>
        <?php if (!empty($search)): ?>
          <a href="<?= BASE_URL ?>/manager/schedule/add-member" class="btn-primary">View All Staff</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

</body>
</html>