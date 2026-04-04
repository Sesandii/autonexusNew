<?php
$base    = rtrim(BASE_URL,'/');
$selCode = $branch_code ?? '';
$prefill = $prefill ?? [];
$prefillVehicleId = (int)($prefill['vehicle_id'] ?? 0);
$prefillServiceId = (int)($prefill['service_id'] ?? 0);
$prefillDate      = $prefill['date'] ?? '';
$prefillTime      = $prefill['time'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($title ?? 'Book Service') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/booking.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css" />
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/home.css" />
  <style>
    /* Slot availability styles */
    .session {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      padding: 12px 16px;
      min-width: 80px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      transition: all 0.2s;
    }
    .session:hover:not(.is-full) {
      border-color: #e74c3c;
      background: #fef2f2;
    }
    .session.is-selected {
      border-color: #e74c3c;
      background: #e74c3c;
      color: #fff;
    }
    .session.is-selected .slot-count {
      color: rgba(255,255,255,0.9);
    }
    .session.is-full {
      background: #fee2e2;
      border-color: #dc2626;
      cursor: not-allowed;
      opacity: 0.9;
    }
    .session.is-full .slot-time {
      color: #991b1b;
      text-decoration: line-through;
    }
    .session.is-full .slot-count {
      color: #dc2626;
      font-weight: 600;
    }
    .slot-time {
      font-weight: 600;
      font-size: 1rem;
    }
    .slot-count {
      font-size: 0.75rem;
      color: #6b7280;
    }
    /* Legend styles */
    .legend {
      display: flex;
      gap: 20px;
      margin-top: 16px;
      font-size: 0.85rem;
    }
    .lg {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .lg-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }
    .available-dot {
      background: #10b981;
      border: 2px solid #059669;
    }
    .full-dot {
      background: #dc2626;
      border: 2px solid #991b1b;
    }

    .customer-main-content {
      margin-left: 240px;
    }

    @media (max-width: 720px) {
      .customer-main-content {
        margin-left: 210px;
      }
    }
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="customer-main-content">
    <div class="container">
    <header class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <h1>Book a Service</h1>
        <p class="subtitle">
          <?= !empty($branch_name) ? 'Selected branch: '.htmlspecialchars($branch_name) : 'Choose your preferred branch and time slot.' ?>
        </p>
        <?php if (!empty($flash)): ?>
          <div class="toast show" style="position:static;margin-top:8px;opacity:1"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
      </div>
      <a href="<?= $base ?>/customer/appointments" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#6c757d;color:#fff;text-decoration:none;border-radius:8px;font-weight:500;">
        Back to Appointments
      </a>
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
                <option value="" disabled <?= $prefillVehicleId ? '' : 'selected' ?>>-- Choose a vehicle --</option>
                <?php foreach ($vehicles as $v):
                  $label = trim(($v['license_plate'] ?? '') . ' — ' . ($v['make'] ?? '') . ' ' . ($v['model'] ?? ''));
                  $selected = ($prefillVehicleId && (int)$v['vehicle_id'] === $prefillVehicleId) ? 'selected' : '';
                ?>
                  <option value="<?= (int)$v['vehicle_id'] ?>" <?= $selected ?>>
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
                <option value="" disabled <?= $prefillServiceId ? '' : 'selected' ?>>-- Choose a service --</option>
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
                      <?php $sel = ($prefillServiceId && (int)$r['service_id'] === $prefillServiceId) ? 'selected' : ''; ?>
                      <option value="<?= (int)$r['service_id'] ?>" <?= $sel ?>>
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
            <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($prefillDate) ?>" required>
            <div class="notes">We’ll show available time slots for your chosen date.</div>
          </div>
        </div>

        <div class="slot-grid" id="slotGrid" style="margin-top:14px;">
          <!-- Morning -->
          <div class="slot">
            <div class="slot__header">Morning</div>
            <div class="slot__sessions" data-period="am">
              <?php foreach (['09:00','10:00','11:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>">
                  <span class="slot-time"><?= $t ?></span>
                  <span class="slot-count">(0/3)</span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <!-- Afternoon -->
          <div class="slot">
            <div class="slot__header">Afternoon</div>
            <div class="slot__sessions" data-period="pm">
              <?php foreach (['13:00','14:00','15:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>">
                  <span class="slot-time"><?= $t ?></span>
                  <span class="slot-count">(0/3)</span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="legend">
          <!-- <span class="lg available"><span class="lg-dot available-dot"></span> Available</span> -->
          <span class="lg full"><span class="lg-dot full-dot"></span> Full (3/3)</span>
        </div>

        <!-- holds time for POST -->
        <input type="hidden" name="time" id="time" value="<?= htmlspecialchars($prefillTime) ?>">
      </section>

      <!-- STEP 5: Confirm -->
      <section class="card">
        <div class="footer-actions">
          <button class="btn-primary" id="bookNow" type="submit">Book Now</button>
        </div>
      </section>

    </form>
    </div>
  </div>

  <div class="toast" id="toast">Booking created!</div>

  <script src="<?= $base ?>/public/assets/js/customer/booking.js" defer></script>
  <script>
    const base = "<?= $base ?>";
    const prefillDate  = "<?= htmlspecialchars($prefillDate, ENT_QUOTES) ?>";
    const prefillTime  = "<?= htmlspecialchars($prefillTime, ENT_QUOTES) ?>";
    const branchCodeInput = document.getElementById('branch_code');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');

    // 1) when a branch is picked, reload page with ?branch=CODE (to load services server-side)
    document.querySelectorAll('input[name="branch_pick"]').forEach(r => {
      r.addEventListener('change', e => {
        const code = e.target.value;
        branchCodeInput.value = code;
        window.location.href = base + '/customer/book?branch=' + encodeURIComponent(code);
      });
    });

    // 2) Fetch and update slot availability
    async function fetchSlotAvailability() {
      const branchCode = branchCodeInput.value.trim();
      const date = dateInput.value;
      
      if (!branchCode || !date) return;
      
      // Check if selected date is today
      const today = new Date();
      const selectedDate = new Date(date + 'T00:00:00');
      const isToday = selectedDate.toDateString() === today.toDateString();
      const currentHour = today.getHours();
      const currentMinute = today.getMinutes();
      
      try {
        const response = await fetch(`${base}/customer/book/slots?branch=${encodeURIComponent(branchCode)}&date=${encodeURIComponent(date)}`);
        const slots = await response.json();
        
        if (slots.error) {
          console.error('Slots error:', slots.error);
          return;
        }
        
        // Update each slot button
        document.querySelectorAll('.session').forEach(btn => {
          const time = btn.dataset.time;
          const slotData = slots[time];
          
          // Parse slot time (e.g., "09:00" -> hour=9, minute=0)
          const [slotHour, slotMinute] = time.split(':').map(Number);
          
          // Check if this time slot has already passed (for today only)
          const isPastSlot = isToday && (slotHour < currentHour || (slotHour === currentHour && slotMinute <= currentMinute));
          
          if (isPastSlot) {
            // Hide the slot if it has already passed
            btn.style.display = 'none';
            // Clear selection if this slot was selected
            if (timeInput.value === time) {
              timeInput.value = '';
            }
          } else {
            // Show the slot
            btn.style.display = '';
            
            if (slotData) {
              const countEl = btn.querySelector('.slot-count');
              countEl.textContent = `(${slotData.booked}/3)`;
              
              if (slotData.full) {
                btn.classList.add('is-full');
                btn.classList.remove('is-selected');
                btn.disabled = true;
                // Clear selection if this slot was selected
                if (timeInput.value === time) {
                  timeInput.value = '';
                }
              } else {
                btn.classList.remove('is-full');
                btn.disabled = false;
              }
            }
          }
        });
      } catch (err) {
        console.error('Failed to fetch slot availability:', err);
      }
    }

    // Fetch slots when date changes
    dateInput.addEventListener('change', fetchSlotAvailability);
    
    // Initial fetch if branch is already selected
    if (branchCodeInput.value && (dateInput.value || prefillDate)) {
      if (!dateInput.value && prefillDate) {
        dateInput.value = prefillDate;
      }
      fetchSlotAvailability().finally(selectPrefillTime);
    } else {
      selectPrefillTime();
    }

    // 3) time slot selection
    document.querySelectorAll('.session').forEach(btn => {
      btn.addEventListener('click', () => {
        if (btn.classList.contains('is-full')) return; // Don't select full slots
        document.querySelectorAll('.session').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
        timeInput.value = btn.dataset.time;
      });
    });

    function selectPrefillTime() {
      if (!prefillTime) return;
      const btn = document.querySelector(`.session[data-time="${prefillTime}"]`);
      if (btn && !btn.classList.contains('is-full')) {
        document.querySelectorAll('.session').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
      }
      timeInput.value = prefillTime;
    }

    // 4) sanity check before submit
    document.getElementById('bookingForm').addEventListener('submit', (e) => {
      const branch = branchCodeInput.value.trim();
      const veh    = document.getElementById('vehicle_id')?.value || '';
      const serv   = document.getElementById('service_id')?.value || '';
      const date   = dateInput.value;
      const time   = timeInput.value;
      if (!branch || !veh || !serv || !date || !time) {
        e.preventDefault();
        alert('Please complete all fields (branch, vehicle, service, date & time).');
      }
    });
  </script>
</body>
</html>
