<?php
$base  = rtrim(BASE_URL, '/');
$items = $appointments ?? [];
$total = count($items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Appointments') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/appointments.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/normalize-ui.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content">

    <!-- Page header -->
    <header class="page-header">
      <div class="page-header-left">
        <h1>Your Appointments</h1>
        <p class="subtitle">Track upcoming visits, completed services, and past cancellations in one place.</p>
      </div>
      <div class="page-header-right">
        <?php if ($total > 0): ?>
          <span class="appointment-count"><?= $total ?> appointment<?= $total === 1 ? '' : 's' ?></span>
        <?php endif; ?>
        <a href="<?= $base ?>/customer/book" class="btn-primary">
          <i class="fa-regular fa-calendar-plus"></i>
          Book service
        </a>
      </div>
    </header>

    <!-- Filters -->
    <section class="filters-bar">
      <span class="filters-label">Filter by status:</span>

      <button class="filter-chip active" data-filter="all" type="button">
        <i class="fa-solid fa-layer-group"></i>
        All
      </button>
      <button class="filter-chip" data-filter="upcoming" type="button">
        <i class="fa-regular fa-clock"></i>
        Upcoming
      </button>
      <button class="filter-chip" data-filter="completed" type="button">
        <i class="fa-regular fa-circle-check"></i>
        Completed
      </button>
      <button class="filter-chip" data-filter="cancelled" type="button">
        <i class="fa-regular fa-circle-xmark"></i>
        Cancelled
      </button>
    </section>

    <!-- Appointments grid -->
    <section class="appointments-grid">
      <?php if (empty($items)): ?>
        <div class="empty-state">
          <i class="fa-regular fa-calendar-xmark"></i>
          <h2>No appointments yet</h2>
          <p>You don’t have any bookings at the moment. Schedule your first service to get started.</p>
          <a href="<?= $base ?>/customer/book">
            <i class="fa-regular fa-calendar-plus"></i>
            Book a service
          </a>
        </div>
      <?php else: ?>
        <?php foreach ($items as $a): ?>
          <?php $statusClass = htmlspecialchars($a['status_class']); ?>
          <article class="appointment-card <?= $statusClass ?>" data-status="<?= $statusClass ?>">
            <div class="card-header">
              <h3>
                <?php
                  $icon = 'fa-screwdriver-wrench';
                  if ($statusClass === 'completed') $icon = 'fa-car-side';
                  if ($statusClass === 'cancelled') $icon = 'fa-ban';
                ?>
                <i class="fa-solid <?= $icon ?>"></i>
                <?= htmlspecialchars($a['service']) ?>
              </h3>
              <span class="status"><?= htmlspecialchars($a['status']) ?></span>
            </div>

            <!-- quick meta row -->
            <div class="card-meta">
              <span class="meta-item">
                <i class="fa-regular fa-calendar"></i>
                <?= htmlspecialchars($a['date']) ?>
              </span>
              <span class="meta-item">
                <i class="fa-regular fa-clock"></i>
                <?= htmlspecialchars($a['time']) ?>
              </span>
              <span class="meta-item">
                <i class="fa-solid fa-location-dot"></i>
                <?= htmlspecialchars($a['branch']) ?>
              </span>
            </div>

            <div class="card-body">
              <?php if (!empty($a['est_completion'])): ?>
                <p><strong>Est. completion:</strong> <?= htmlspecialchars($a['est_completion']) ?></p>
              <?php endif; ?>
            </div>

            <div class="card-footer">
              <?php if ($statusClass === 'upcoming'): ?>
                <form method="post"
                      action="<?= $base ?>/customer/appointments/cancel"
                      onsubmit="return confirm('Cancel this appointment?');">
                  <input type="hidden" name="appointment_id" value="<?= (int)$a['appointment_id'] ?>">
                  <button class="btn-danger" type="submit">
                    <i class="fa-regular fa-circle-xmark"></i>
                    Cancel
                  </button>
                </form>

                <a class="btn-ghost"
                   href="<?= $base ?>/customer/booking?reschedule=<?= (int)$a['appointment_id'] ?>">
                  <i class="fa-regular fa-eye"></i>
                  View details
                </a>

              <?php elseif ($statusClass === 'completed'): ?>
                <a class="btn-success"
                   href="<?= $base ?>/customer/rate-service?appointment=<?= (int)$a['appointment_id'] ?>">
                  <i class="fa-regular fa-star"></i>
                  Leave review
                </a>

                <a class="btn-primary-small"
                   href="<?= $base ?>/customer/booking?rebook=<?= (int)$a['appointment_id'] ?>">
                  <i class="fa-solid fa-rotate-right"></i>
                  Rebook
                </a>

              <?php else: ?>
                <a class="btn-primary-small"
                   href="<?= $base ?>/customer/booking?rebook=<?= (int)$a['appointment_id'] ?>">
                  <i class="fa-solid fa-rotate-right"></i>
                  Rebook
                </a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <!-- Small JS for filter chips -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const chips = document.querySelectorAll('.filter-chip');
      const cards = document.querySelectorAll('.appointment-card');

      chips.forEach(chip => {
        chip.addEventListener('click', () => {
          const filter = chip.getAttribute('data-filter');

          chips.forEach(c => c.classList.remove('active'));
          chip.classList.add('active');

          cards.forEach(card => {
            const status = card.getAttribute('data-status');
            if (filter === 'all' || status === filter) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
            }
          });
        });
      });
    });
  </script>
</body>
</html>
