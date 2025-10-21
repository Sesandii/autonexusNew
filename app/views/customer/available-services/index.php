<!-- views/customer/available-services/index.php -->
<?php
$base = rtrim(BASE_URL,'/');
$branchTitle = '';
if (!empty($branch_name)) {
  $branchTitle = " — " . htmlspecialchars($branch_name);
} elseif (!empty($branch_code)) {
  $branchTitle = " — " . htmlspecialchars($branch_code);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars(($title ?? 'Available Services') . $branchTitle) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/available-services.css" />
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<!-- NAVBAR -->
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="<?= rtrim(BASE_URL,'/') ?>/">
        <img src="<?= rtrim(BASE_URL,'/') ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo" class="brand-logo" />
        <span class="brand-text">AutoNexus</span>
      </a>

      <nav class="main-nav" id="mainNav">
        <a href="<?= rtrim(BASE_URL,'/') ?>/">Home</a>
        <a href="#services" id="servicesNavLink">Services</a>
        <a href="#how">How It Works</a>
        <a href="#benefits">Why Choose Us</a>
        <a href="#reviews">Reviews</a>
        <a href="#contact">Contact</a>
        <a href="<?= rtrim(BASE_URL,'/') ?>/login" class="btn-outline">Login</a>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <header class="hero">
    <div class="hero-text">
      <h1><i class="fa-solid fa-car-side"></i> Available Services<?= $branchTitle ?></h1>
      <p>Explore premium automotive services tailored to your ride.</p>
    </div>
  </header>

  <main class="content-wrapper">
    <!-- FILTER TABS -->
    <div class="filters" role="tablist" aria-label="Service categories">
      <button class="chip active" data-filter="all" role="tab" aria-selected="true"><i class="fa-solid fa-layer-group"></i> All</button>
      <button class="chip" data-filter="maintenance" role="tab"><i class="fa-solid fa-screwdriver-wrench"></i> Maintenance</button>
      <button class="chip" data-filter="tyre" role="tab"><i class="fa-solid fa-circle-notch"></i> Tyre</button>
      <button class="chip" data-filter="cleaning" role="tab"><i class="fa-solid fa-broom"></i> Cleaning</button>
      <button class="chip" data-filter="nano" role="tab"><i class="fa-solid fa-spray-can-sparkles"></i> Nano</button>
      <button class="chip" data-filter="paint" role="tab"><i class="fa-solid fa-paint-roller"></i> Paint</button>
      <button class="chip" data-filter="electrical" role="tab"><i class="fa-solid fa-bolt"></i> Electrical</button>
      <button class="chip" data-filter="brakes" role="tab"><i class="fa-solid fa-circle-stop"></i> Brakes</button>
      <button class="chip" data-filter="ac" role="tab"><i class="fa-regular fa-snowflake"></i> A/C</button>
      <button class="chip" data-filter="battery" role="tab"><i class="fa-solid fa-car-battery"></i> Battery</button>
      <button class="chip" data-filter="suspension" role="tab"><i class="fa-solid fa-arrows-up-down-left-right"></i> Suspension</button>
      <button class="chip" data-filter="glass" role="tab"><i class="fa-regular fa-window-maximize"></i> Glass</button>
      <button class="chip" data-filter="inspection" role="tab"><i class="fa-solid fa-clipboard-check"></i> Inspection</button>
      <button class="chip" data-filter="addons" role="tab"><i class="fa-solid fa-gift"></i> Add-ons</button>
    </div>

    <div class="grid-layout">
      <!-- SERVICES COLUMN -->
      <section class="services" id="servicesRoot">
        <?php
        // ===== Dynamic render from DB (cards only) =====
        $ORDER = ['maintenance','tyre','cleaning','nano','paint','electrical','brakes','ac','battery','suspension','glass','inspection','addons','other'];

        function slug_type(?string $t): string {
          $t = strtolower(trim($t ?? ''));
          $map = [
            'maintenance' => 'maintenance',
            'tyre'        => 'tyre',
            'tire'        => 'tyre',
            'cleaning'    => 'cleaning',
            'nano'        => 'nano',
            'paint'       => 'paint',
            'electrical'  => 'electrical',
            'brakes'      => 'brakes',
            'brake'       => 'brakes',
            'a/c'         => 'ac',
            'ac'          => 'ac',
            'battery'     => 'battery',
            'suspension'  => 'suspension',
            'glass'       => 'glass',
            'inspection'  => 'inspection',
            'add-ons'     => 'addons',
            'addons'      => 'addons',
          ];
          return $map[$t] ?? ($t ?: 'other');
        }

        // Group services by type slug
        $groups = [];
        foreach (($services ?? []) as $s) {
          $cat = slug_type($s['type_name'] ?? '');
          $groups[$cat][] = $s;
        }

        // Render categories in fixed order, only if they have services
        foreach ($ORDER as $cat):
          if (empty($groups[$cat])) continue;
          $label = ucfirst($cat === 'ac' ? 'A/C' : $cat);
        ?>
          <h2 class="category" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($label) ?></h2>
          <div class="service-grid" data-cat="<?= htmlspecialchars($cat) ?>">
            <?php foreach ($groups[$cat] as $row): ?>
              <article class="card"
                       data-name="<?= htmlspecialchars($row['service_name']) ?>"
                       data-price="<?= htmlspecialchars(number_format((float)$row['default_price'], 2)) ?>">
                <div class="card-head">
                  <i class="fa-solid fa-screwdriver-wrench icon"></i>
                  <h3><?= htmlspecialchars($row['service_name']) ?></h3>
                </div>
                <p><?= htmlspecialchars($row['description'] ?? 'Professional service for your vehicle.') ?></p>
                <div class="meta">
                  <span><i class="fa-regular fa-clock"></i>
                    <?= htmlspecialchars((string)($row['base_duration_minutes'] ?? 60)) ?> min
                  </span>
                  <span class="price">$<?= htmlspecialchars(number_format((float)$row['default_price'], 2)) ?></span>
                </div>
                <button class="add"><i class="fa-solid fa-plus"></i> Add</button>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>

        <?php if (empty($services)): ?>
          <p class="muted">No services found for this branch.</p>
        <?php endif; ?>
        <!-- ===== /Dynamic render from DB ===== -->
      </section>

      <!-- CART COLUMN -->
      <aside class="cart">
        <div class="cart-box">
          <h3><i class="fa-solid fa-cart-shopping"></i> Cart Summary</h3>
          <ul id="cart-items" class="cart-items">
            <li class="muted">No services added yet.</li>
          </ul>

          <div class="totals">
            <div><span>Subtotal</span> <strong>$<span id="subtotal">0.00</span></strong></div>
            <div><span>Tax (8%)</span> <strong>$<span id="tax">0.00</span></strong></div>
            <div class="grand"><span>Total</span> <strong>$<span id="total">0.00</span></strong></div>
          </div>

          <button id="calculate" class="btn dark"><i class="fa-solid fa-calculator"></i> Calculate</button>
          <button id="checkout" class="btn accent"><i class="fa-solid fa-calendar-check"></i> Proceed to Booking</button>
        </div>
      </aside>
    </div>
  </main>

  <footer class="footer">© <?= date('Y') ?> AutoNexus — Drive Smarter.</footer>

  <script>const BASE_URL="<?= $base ?>"; const BRANCH_CODE="<?= htmlspecialchars($branch_code ?? '') ?>";</script>
  <script src="<?= $base ?>/public/assets/js/customer/available-services.js"></script>
</body>
</html>
