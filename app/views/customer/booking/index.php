<?php
$base = rtrim(BASE_URL,'/');
$selCode = $branch_code ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($title ?? 'Book Service') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/booking.css" />
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/home.css" />
</head>
<body>
  <div class="container">
    <header class="page-header">
      <h1>Book a Service</h1>
      <p class="subtitle">
        <?= !empty($branch_name) ? 'Selected branch: '.htmlspecialchars($branch_name) : 'Choose your preferred branch and time slot.' ?>
      </p>
    </header>

    <!-- STEP 1: Branch -->
    <section class="card">
      <div class="step-title">
        <span class="step-badge">1</span>
        <h3>Select Branch</h3>
      </div>

      <div class="branch-grid">
        <?php foreach ($branches as $b):
          $checked = ($selCode && $selCode === $b['branch_code']) ? 'checked' : ''; ?>
          <label class="branch-card">
            <input type="radio" name="branch" value="<?= htmlspecialchars($b['branch_code']) ?>" <?= $checked ?>>
            <div class="branch-card__content">
              <strong><?= htmlspecialchars($b['branch_name']) ?></strong><br>
              <small><?= htmlspecialchars($b['branch_code']) ?></small>
            </div>
          </label>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- STEP 2: Vehicle (NEW) -->
    <section class="card">
      <div class="step-title">
        <span class="step-badge">2</span>
        <h3>Select Vehicle</h3>
      </div>

      <?php if (!empty($vehicles)): ?>
        <div class="date-row" style="gap:12px;">
          <div class="date-field" style="min-width:260px;">
            <label for="vehicle_id">Your vehicles</label><br>
            <select id="vehicle_id">
              <option value="" selected disabled>-- Choose a vehicle --</option>
              <?php foreach ($vehicles as $v): 
                $label = trim(($v['license_plate'] ?? '') . ' — ' . ($v['make'] ?? '') . ' ' . ($v['model'] ?? '')); ?>
                <option value="<?= (int)$v['vehicle_id'] ?>">
                  <?= htmlspecialchars($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="notes">We’ll attach this booking to the selected vehicle.</div>
          </div>
        </div>
      <?php else: ?>
        <p class="notes">
          You don’t have any vehicles saved yet.
          <a href="<?= $base ?>/customer/profile">Add a vehicle in your Profile</a> to continue.
        </p>
      <?php endif; ?>
    </section>

    <!-- STEP 3: Services (kept as-is, just bumped step number) -->
    <section class="card">
      <div class="step-title">
        <span class="step-badge">3</span>
        <h3>Select Services</h3>
      </div>

      <div class="accordion-controls">
        <button class="btn-ghost" id="expandAll">Expand all</button>
        <button class="btn-ghost" id="collapseAll">Collapse all</button>
      </div>

      <div class="accordion" id="serviceAccordion">
        <details class="acc-item" open>
          <summary>Maintenance <i class="chev">▾</i></summary>
          <div class="acc-panel">
            <label><input type="checkbox" data-name="Oil Change (Synthetic)" data-price="54.99"> Oil Change (Synthetic) — $54.99</label>
            <label><input type="checkbox" data-name="Comprehensive 10k Service" data-price="169.99"> Comprehensive 10k Service — $169.99</label>
            <label><input type="checkbox" data-name="Engine Tune Up" data-price="149.99"> Engine Tune Up — $149.99</label>
          </div>
        </details>

        <details class="acc-item">
          <summary>Tyre <i class="chev">▾</i></summary>
          <div class="acc-panel">
            <label><input type="checkbox" data-name="Wheel Alignment" data-price="89.99"> Wheel Alignment — $89.99</label>
            <label><input type="checkbox" data-name="Puncture Repair" data-price="24.99"> Puncture Repair — $24.99</label>
          </div>
        </details>
      </div>
    </section>

    <!-- STEP 4: Date & Slot (unchanged UI for now) -->
    <section class="card">
      <div class="step-title">
        <span class="step-badge">4</span>
        <h3>Date & Time</h3>
      </div>

      <div class="date-row">
        <div class="date-field">
          <label for="date">Preferred date</label><br>
          <input type="date" id="date" min="<?= date('Y-m-d') ?>">
          <div class="notes">We’ll show available time slots for your chosen date.</div>
        </div>
      </div>

      <div class="slot-grid" id="slotGrid" style="margin-top:14px;">
        <div class="slot">
          <div class="slot__header">Morning</div>
          <div class="slot__sessions" data-period="am"></div>
        </div>
        <div class="slot">
          <div class="slot__header">Afternoon</div>
          <div class="slot__sessions" data-period="pm"></div>
        </div>
      </div>

      <div class="legend">
        <span class="lg available">Available</span>
        <span class="lg reserved">Reserved</span>
        <span class="lg ongoing">Ongoing</span>
        <span class="lg completed">Completed</span>
      </div>
    </section>

    <!-- STEP 5: Confirm -->
    <section class="card">
      <div class="footer-actions">
        <button class="btn-primary" id="bookNow">Book Now</button>
      </div>
    </section>
  </div>

  <div class="toast" id="toast">Booking created!</div>

  <script>
    const BASE_URL    = "<?= $base ?>";
    const SELECTED_BRANCH_CODE = "<?= htmlspecialchars($branch_code ?? '') ?>";
    const PRESELECTED_ITEMS = <?= json_encode($items_param ?: '[]') ?>;
  </script>
  <script src="<?= $base ?>/public/assets/js/customer/booking.js"></script>
</body>
</html>
