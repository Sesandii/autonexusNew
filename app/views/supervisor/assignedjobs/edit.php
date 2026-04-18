<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Work Order Details</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-assignedjobs.css"/>
</head>

<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
  <main class="main">
  <div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Assigned Jobs <span class="sep">&gt;</span> 
    #<?= htmlspecialchars($job['work_order_id']) ?> <span class="sep">&gt;</span> 
    <span class="active-page">Edit</span>
  </div>
    <header>
      <h1>Job Details</h1>
    </header>
    <section class="job-sec">
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
    <div class="info-row">
        <span>Colour</span>
        <span><?= $job['color'] ?></span>
    </div>
    <div class="info-row">
        <span>Mileage</span>
        <span><?= $job['current_mileage'] ?></span>
    </div>
</div>

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
    <div class="info-row">
        <span>Contact</span>
        <span><?= $job['phone'] ?></span>
    </div>
    <div class="info-row">
        <span>Street address</span>
        <span><?= $job['street_address'] ?></span>
    </div>
    <div class="info-row">
        <span>City</span>
        <span><?= $job['city'] ?></span>
    </div>
    <div class="info-row">
        <span>State</span>
        <span><?= $job['state'] ?></span>
    </div>
</div>

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
                                <button type="submit" class="btn-action undo">Undo</button>
                            <?php else: ?>
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn-action done">Done</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="details-box info-card photos-box">
    <h2 class="info-title">Service Photos</h2>

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

    <?php if (!empty($job['photos'])): ?>
        <div class="photo-grid">
            <?php foreach ($job['photos'] as $photo): ?>
                <div class="photo-card">
                    <img
                        src="/autonexus/public/assets/img/service_photos/<?= htmlspecialchars($photo['file_name']) ?>"
                        class="photo-img"
                        onclick="openModal(this.src)"
                    >
                    <form
    action="/autonexus/supervisor/assignedjobs/deletePhoto"
    method="POST"
    class="deleteForm"
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
    <div class="back-button-wrapper">
  <a href="/autonexus/supervisor/assignedjobs" class="btn-back">
    Back
  </a>
</div>
  </main>

<div id="imageModal" style="display:none;
     position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.8);
     justify-content:center; align-items:center;
     z-index:9999;"
     onclick="closeModal()"
>
    <img id="modalImage" style="max-width:90%; max-height:90%; border-radius:10px;">
</div>

<div id="deleteModal" class="modal-overlay">
  <div class="modal-box">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this photo?</p>
    <div class="modal-actions">
      <button id="cancelDelete" class="btn small">Cancel</button>
      <button id="confirmDelete" class="btn small danger">Delete</button>
    </div>
  </div>
</div>

<script>
function openModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

let formToSubmit = null;

document.querySelectorAll('.deleteForm').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        formToSubmit = this;
        document.getElementById('deleteModal').style.display = 'flex';
    });
});

document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').style.display = 'none';
    formToSubmit = null;
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (formToSubmit) {
        formToSubmit.submit(); 
    }
});

</script>


</body>
</html>
