<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Customer #<?= (int)$c['customer_id'] ?></title>

  <!-- Use BASE_URL so CSS always loads -->
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">

  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/customers">Customers</a>
      <span>›</span>
      <span>Customer #<?= (int)$c['customer_id'] ?></span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon">
          <i class="fa-solid fa-user"></i>
        </div>
        <div>
          <h2>Customer Profile</h2>
          <p>Overview of contact details and account status.</p>
        </div>
      </div>

      <div class="page-chip">
        ID: <?= (int)$c['customer_id'] ?>
      </div>
    </div>
  </header>

  <?php
    $status = $c['status'] ?? 'active';
    $pillClass = $status === 'inactive'
      ? 'status--inactive'
      : ($status === 'pending' ? 'status--pending' : 'status--active');
  ?>

  <section class="detail-card" aria-labelledby="customer-heading">
    <div class="detail-card-topbar"></div>
    <div class="detail-card-inner">
      <div class="detail-heading">
        <div>
          <div class="detail-heading-name" id="customer-heading">
            <?= htmlspecialchars(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?: 'Unnamed Customer') ?>
          </div>
          <div class="detail-item-value" style="margin-top:3px;font-weight:400;color:#6b7280;">
            <?= htmlspecialchars($c['email'] ?? 'No email on file') ?>
          </div>
        </div>

        <div class="detail-meta-chips">
          <span class="status-pill <?= $pillClass ?>">
            <span class="dot"></span><?= htmlspecialchars(ucfirst($status)) ?>
          </span>
          <span class="meta-chip">
            <i class="fa-regular fa-calendar"></i>
            Customer since <?= htmlspecialchars(substr($c['created_at'] ?? '—',0,10)) ?>
          </span>
          <span class="meta-chip">
            <i class="fa-regular fa-id-badge"></i>
            Code: <?= htmlspecialchars($c['customer_code'] ?? '—') ?>
          </span>
        </div>
      </div>

      <div class="detail-grid">
        <div>
          <p class="detail-item-label">First Name</p>
          <p class="detail-item-value"><?= htmlspecialchars($c['first_name'] ?? '—') ?></p>
        </div>
        <div>
          <p class="detail-item-label">Last Name</p>
          <p class="detail-item-value"><?= htmlspecialchars($c['last_name'] ?? '—') ?></p>
        </div>

        <div>
          <p class="detail-item-label">Email</p>
          <p class="detail-item-value"><?= htmlspecialchars($c['email'] ?? '—') ?></p>
        </div>
        <div>
          <p class="detail-item-label">Phone</p>
          <p class="detail-item-value"><?= htmlspecialchars($c['phone'] ?? '—') ?></p>
        </div>

        <div>
          <p class="detail-item-label">Created At</p>
          <p class="detail-item-value"><?= htmlspecialchars($c['created_at'] ?? '—') ?></p>
        </div>
      </div>
    </div>

    <div class="detail-footer">
      <a class="btn-primary"
         href="<?= rtrim(BASE_URL, '/') ?>/admin/customers/<?= (int)$c['customer_id'] ?>/edit">
        <i class="fas fa-pen"></i>&nbsp;Edit Customer
      </a>

      <a class="btn-secondary"
         href="<?= rtrim(BASE_URL, '/') ?>/admin/customers">
        Back to Customers
      </a>
    </div>
  </section>
</main>
</body>
</html>
