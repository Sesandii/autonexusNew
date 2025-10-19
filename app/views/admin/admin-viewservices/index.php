<?php $current = 'services'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Service Management</title>
  <!-- Shared neutral styles -->
   <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/services/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
  </style>

</head>
<body>
 <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <div class="container">
    <!-- Main Content -->
    <main class="main-content">
      <section class="service-section">
        <div class="section-header">
          <div>
            <h1 class="admin-title">Service Management</h1>
            <p>Manage your service offerings</p>
          </div>
          <button class="btn-primary">+ Add New Service</button>
        </div>

        <div class="tabs">
          <button class="tab active" data-tab="all">All</button>
          <button class="tab" data-tab="maintenance">Maintenance</button>
          <button class="tab" data-tab="inspection">Inspection</button>
          <button class="tab" data-tab="diagnostics">Diagnostics</button>
          <button class="tab" data-tab="repair">Repair</button>
          <button class="tab" data-tab="replacement">Replacement</button>
        </div>

        <div class="table-container">
          <table class="service-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- All Services -->
              <tr class="maintenance">
                <td>Oil Change</td>
                <td>Standard oil change service</td>
                <td><span class="badge">Maintenance</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
              <tr class="inspection">
                <td>Brake Inspection</td>
                <td>Complete brake system inspection</td>
                <td><span class="badge">Inspection</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
              <tr class="maintenance">
                <td>Tire Rotation</td>
                <td>Rotate tires to ensure even wear</td>
                <td><span class="badge">Maintenance</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
              <tr class="diagnostics">
                <td>Engine Diagnostics</td>
                <td>Computer diagnostics for engine issues</td>
                <td><span class="badge">Diagnostics</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
              <tr class="repair">
                <td>AC Service</td>
                <td>Air conditioning system service</td>
                <td><span class="badge">Repair</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
              <tr class="replacement">
                <td>Windshield Replacement</td>
                <td>Replace damaged windshield</td>
                <td><span class="badge">Replacement</span></td>
                <td class="actions">
                  <span class="icon-btn">✏️</span>
                  <span class="icon-btn">🗑️</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <script src="script.js"></script>
</body>
</html>
