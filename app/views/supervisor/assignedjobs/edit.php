<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Work Order Details</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-assignedjobs.css"/>
  <style>
    .details-box {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 25px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .details-box h2 {
      margin-bottom: 10px;
      color: #333;
    }
    .photo-img {
      width: 200px;
      margin: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
    </div>

    <h2>AUTONEXUS</h2>

    <a href="/autonexus/supervisor/dashboard">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/supervisor/workorders">
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="/autonexus/supervisor/assignedjobs" class="nav active">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned
    </a>
    <a href="/autonexus/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="/autonexus/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
    </a>
    <a href="/autonexus/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
    </a>
    <a href="/autonexus/supervisor/reports">
      <img src="/autonexus/public/assets/img/Inspection.png"/>Report
    </a>
  </div>
  <main class="main">
    <header>
      <h2>Job Details</h2>
    </header>
    <section class="job-section">
      <!-- WORK ORDER DETAILS -->
<div class="details-box info-card">
    <h2 class="info-title">Work Order Details</h2>
    <div class="info-row">
    <span>Work order ID</span>
    <span> <?= $job['work_order_id'] ?></span>
</div>
<div class="info-row">
    <span>Status</span>
        <span class="status-pill <?= strtolower($job['status']) ?>">
            <?= ucfirst($job['status']) ?>
        </span>
    </div>

    <div class="info-row">
        <span>Started At</span>
        <span><?= $job['started_at'] ?></span>
    </div>

    <div class="info-row">
        <span>Completed At</span>
        <span><?= $job['completed_at'] ?? 'Not Completed' ?></span>
    </div>

    <div class="info-row">
        <span>Service</span>
        <span><?= $job['service_name'] ?></span>
    </div>
</div>

<!-- VEHICLE INFO -->
<div class="details-box info-card">
    <h2 class="info-title">Vehicle Information</h2>

    <div class="info-row">
        <span>Vehicle No</span>
        <span><?= $job['license_plate'] ?></span>
    </div>

    <div class="info-row">
        <span>Make</span>
        <span><?= $job['make'] ?></span>
    </div>

    <div class="info-row">
        <span>Model</span>
        <span><?= $job['model'] ?></span>
    </div>
</div>

<!-- CUSTOMER INFO -->
<div class="details-box info-card">
    <h2 class="info-title">Customer Information</h2>

    <div class="info-row">
        <span>Name</span>
        <span><?= $job['customer_first_name'] . ' ' . $job['customer_last_name'] ?></span>
    </div>

    <div class="info-row">
        <span>Customer Code</span>
        <span><?= $job['customer_code'] ?></span>
    </div>
</div>


      <!-- CHECKLIST -->
<div class="details-box checklist-box">
    <h3 class="checklist-title">Service Checklist</h3>

    <?php if (empty($job['checklist'])): ?>
        <p class="checklist-empty">No checklist items found.</p>
    <?php else: ?>
        <table class="checklist-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($job['checklist'] as $i => $item): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>

                    <td>
                        <span class="status-badge <?= $item['status'] === 'completed' ? 'done' : 'pending' ?>">
                            <?= ucfirst($item['status']) ?>
                        </span>
                    </td>

                    <td>
                        <form method="post"
                              action="/autonexus/supervisor/checklist/toggle"
                              class="inline-form">

                            <input type="hidden" name="checklist_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="work_order_id" value="<?= $job['work_order_id'] ?>">

                            <?php if ($item['status'] === 'completed'): ?>
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn-action undo">↩ Undo</button>
                            <?php else: ?>
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn-action done">✔ Done</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

     <!-- SERVICE PHOTOS -->
<div class="details-box info-card">
    <h2 class="info-title">Service Photos</h2>

    <!-- UPLOAD FORM -->
    <form
        action="/autonexus/supervisor/assignedjobs/uploadPhoto"
        method="POST"
        enctype="multipart/form-data"
        class="photo-upload-form"
    >
        <input type="hidden" name="work_order_id" value="<?= $job['work_order_id'] ?>">

        <input type="file" name="service_photo" accept="image/*" required>

        <button type="submit" class="btn primary">Upload Photo</button>
    </form>

    <!-- PHOTO GRID -->
    <?php if (!empty($job['photos'])): ?>
        <div class="photo-grid">
            <?php foreach ($job['photos'] as $photo): ?>
                <div class="photo-card">
                    <img
                        src="/autonexus/public/assets/img/service_photos/<?= htmlspecialchars($photo['file_name']) ?>"
                        class="photo-img"
                        onclick="openModal(this.src)"
                    >

                    <!-- DELETE -->
                    <form
                        action="/autonexus/supervisor/assignedjobs/deletePhoto"
                        method="POST"
                        onsubmit="return confirm('Delete this photo?');"
                    >
                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                        <input type="hidden" name="work_order_id" value="<?= $job['work_order_id'] ?>">
                        <button type="submit" class="btn danger small">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="empty-text">No photos uploaded.</p>
    <?php endif; ?>
</div>


    </section>
  </main>

  <!-- 🔍 IMAGE MODAL -->
<div id="imageModal" style="display:none;
     position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.8);
     justify-content:center; align-items:center;
     z-index:9999;"
     onclick="closeModal()"
>
    <img id="modalImage" style="max-width:90%; max-height:90%; border-radius:10px;">
</div>

<script>
function openModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>


</body>
</html>
