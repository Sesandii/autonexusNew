<?php
$base    = rtrim(BASE_URL,'/');
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
      <?php if (!empty($flash)): ?>
        <div class="toast show" style="position:static;margin-top:8px;opacity:1"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>
    </header>

    <!-- Wrap everything in a POST form -->
    <form action="<?= $base ?>/customer/book" method="post" id="bookingForm">

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
              <input type="radio" name="branch_pick" value="<?= htmlspecialchars($b['branch_code']) ?>" <?= $checked ?>>
              <div class="branch-card__content">
                <strong><?= htmlspecialchars($b['branch_name']) ?></strong><br>
                <small><?= htmlspecialchars($b['branch_code']) ?></small>
              </div>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- will be submitted -->
        <input type="hidden" name="branch_code" id="branch_code" value="<?= htmlspecialchars($selCode) ?>">
        <div class="notes">Tip: changing branch reloads services for that branch.</div>
      </section>

      <!-- STEP 2: Vehicle -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">2</span>
          <h3>Select Vehicle</h3>
        </div>

        <?php if (!empty($vehicles)): ?>
          <div class="date-row" style="gap:12px;">
            <div class="date-field" style="min-width:260px;">
              <label for="vehicle_id">Your vehicles</label><br>
              <select id="vehicle_id" name="vehicle_id" required>
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

      <!-- STEP 3: Services (loaded from DB for chosen branch) -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">3</span>
          <h3>Select Service</h3>
        </div>

        <?php if (!empty($services)): ?>
          <div class="date-row">
            <div class="date-field" style="min-width:320px;">
              <label for="service_id">Available services at this branch</label><br>
              <select id="service_id" name="service_id" required>
                <option value="" selected disabled>-- Choose a service --</option>
                <?php
                  // Group by type_name as <optgroup>
                  $byType = [];
                  foreach ($services as $s) {
                    $byType[$s['type_name']][] = $s;
                  }
                  foreach ($byType as $type => $rows):
                ?>
                  <optgroup label="<?= htmlspecialchars($type) ?>">
                    <?php foreach ($rows as $r): ?>
                      <option value="<?= (int)$r['service_id'] ?>">
                        <?= htmlspecialchars($r['service_name']) ?> — $<?= number_format((float)$r['default_price'], 2) ?>
                      </option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
              <div class="notes">This list updates when you change the branch.</div>
            </div>
          </div>
        <?php else: ?>
          <p class="notes">Pick a branch first to see available services.</p>
        <?php endif; ?>
      </section>

      <!-- STEP 4: Date & Slot -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">4</span>
          <h3>Date & Time</h3>
        </div>

        <div class="date-row">
          <div class="date-field">
            <label for="date">Preferred date</label><br>
            <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required>
            <div class="notes">We’ll show available time slots for your chosen date.</div>
          </div>
        </div>

        <div class="slot-grid" id="slotGrid" style="margin-top:14px;">
          <!-- Morning -->
          <div class="slot">
            <div class="slot__header">Morning</div>
            <div class="slot__sessions" data-period="am">
              <?php foreach (['09:00','10:00','11:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>"><?= $t ?></button>
              <?php endforeach; ?>
            </div>
          </div>
          <!-- Afternoon -->
          <div class="slot">
            <div class="slot__header">Afternoon</div>
            <div class="slot__sessions" data-period="pm">
              <?php foreach (['13:00','14:00','15:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>"><?= $t ?></button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="legend">
          <span class="lg available">Available</span>
          <span class="lg reserved">Reserved</span>
          <span class="lg ongoing">Ongoing</span>
          <span class="lg completed">Completed</span>
        </div>

        <!-- holds time for POST -->
        <input type="hidden" name="time" id="time">
      </section>

      <!-- STEP 5: Confirm -->
      <section class="card">
        <div class="footer-actions">
          <button class="btn-primary" id="bookNow" type="submit">Book Now</button>
        </div>
      </section>

    </form>
  </div>

  <div class="toast" id="toast">Booking created!</div>

  <script src="<?= $base ?>/public/assets/js/customer/booking.js" defer></script>
  <script>
    const base = "<?= $base ?>";
    // 1) when a branch is picked, reload page with ?branch=CODE (to load services server-side)
    document.querySelectorAll('input[name="branch_pick"]').forEach(r => {
      r.addEventListener('change', e => {
        const code = e.target.value;
        document.getElementById('branch_code').value = code;
        window.location.href = base + '/customer/book?branch=' + encodeURIComponent(code);
      });
    });

    // 2) time slot selection
    const timeInput = document.getElementById('time');
    document.querySelectorAll('.session').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.session').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
        timeInput.value = btn.dataset.time;
      });
    });

    // 3) sanity check before submit
    document.getElementById('bookingForm').addEventListener('submit', (e) => {
      const branch = document.getElementById('branch_code').value.trim();
      const veh    = document.getElementById('vehicle_id')?.value || '';
      const serv   = document.getElementById('service_id')?.value || '';
      const date   = document.getElementById('date').value;
      const time   = document.getElementById('time').value;
      if (!branch || !veh || !serv || !date || !time) {
        e.preventDefault();
        alert('Please complete all fields (branch, vehicle, service, date & time).');
      }
    });
  </script>
</body>
</html>
