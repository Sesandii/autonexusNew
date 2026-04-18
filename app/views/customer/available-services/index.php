<!-- views/customer/available-services/index.php -->
<?php
$base = rtrim(BASE_URL,'/');
$branchTitle = '';
if (!empty($branch_name)) {
  $branchTitle = " — " . htmlspecialchars($branch_name);
} elseif (!empty($branch_code)) {
  $branchTitle = " — " . htmlspecialchars($branch_code);
}
$availServicesCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/available-services.css') ?: time();
$availServicesJsVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/js/customer/available-services.js') ?: time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars(($title ?? 'Available Services') . $branchTitle) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/available-services.css?v=<?= (int)$availServicesCssVersion ?>" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="customer-main-shell customer-layout-main">

  <?php
    $headerIcon = 'fa-solid fa-car-side';
    $headerTitle = 'Available Services' . $branchTitle;
    $headerSubtitle = 'Pick one service and book instantly with clear pricing.';
    include APP_ROOT . '/views/partials/customer-page-header.php';
  ?>

  <main class="content-wrapper available-services-page">
    <section class="trust-strip" aria-label="Why customers choose AutoNexus">
      <article class="trust-card"><i class="fa-solid fa-award"></i><span>Certified Technicians</span></article>
      <article class="trust-card"><i class="fa-solid fa-shield-check"></i><span>Genuine Parts</span></article>
      <article class="trust-card"><i class="fa-solid fa-receipt"></i><span>Transparent Pricing</span></article>
      <article class="trust-card"><i class="fa-solid fa-bolt"></i><span>Same-Day Service Available</span></article>
    </section>

    <div class="service-filters" role="tablist" aria-label="Service categories">
      <button type="button" class="service-chip active" data-filter="all" role="tab" aria-selected="true"><i class="fa-solid fa-layer-group"></i> All</button>
      <button type="button" class="service-chip" data-filter="maintenance" role="tab"><i class="fa-solid fa-screwdriver-wrench"></i> Maintenance</button>
      <button type="button" class="service-chip" data-filter="tyre" role="tab"><i class="fa-solid fa-circle-notch"></i> Tyre</button>
      <button type="button" class="service-chip" data-filter="cleaning" role="tab"><i class="fa-solid fa-broom"></i> Cleaning</button>
      <button type="button" class="service-chip" data-filter="nano" role="tab"><i class="fa-solid fa-spray-can-sparkles"></i> Nano</button>
      <button type="button" class="service-chip" data-filter="paint" role="tab"><i class="fa-solid fa-paint-roller"></i> Paint</button>
      <button type="button" class="service-chip" data-filter="electrical" role="tab"><i class="fa-solid fa-bolt"></i> Electrical</button>
      <button type="button" class="service-chip" data-filter="brakes" role="tab"><i class="fa-solid fa-circle-stop"></i> Brakes</button>
      <button type="button" class="service-chip" data-filter="ac" role="tab"><i class="fa-regular fa-snowflake"></i> A/C</button>
      <button type="button" class="service-chip" data-filter="battery" role="tab"><i class="fa-solid fa-car-battery"></i> Battery</button>
      <button type="button" class="service-chip" data-filter="suspension" role="tab"><i class="fa-solid fa-arrows-up-down-left-right"></i> Suspension</button>
      <button type="button" class="service-chip" data-filter="glass" role="tab"><i class="fa-regular fa-window-maximize"></i> Glass</button>
      <button type="button" class="service-chip" data-filter="inspection" role="tab"><i class="fa-solid fa-clipboard-check"></i> Inspection</button>
      <button type="button" class="service-chip" data-filter="addons" role="tab"><i class="fa-solid fa-gift"></i> Add-ons</button>
    </div>

    <section class="services" id="servicesRoot">
      <?php
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

      $groups = [];
      foreach (($services ?? []) as $s) {
        $cat = slug_type($s['type_name'] ?? '');
        $groups[$cat][] = $s;
      }

      foreach ($ORDER as $cat):
        if (empty($groups[$cat])) continue;
        $label = ucfirst($cat === 'ac' ? 'A/C' : $cat);
      ?>
        <h2 class="service-category" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($label) ?></h2>
        <div class="service-grid" data-cat="<?= htmlspecialchars($cat) ?>">
          <?php foreach ($groups[$cat] as $row): ?>
            <?php
              $serviceId = (int)$row['service_id'];
              $bookParams = ['service_id' => (string)$serviceId];
              if (!empty($branch_code)) {
                $bookParams['branch'] = (string)$branch_code;
              }
              $bookUrl = $base . '/customer/book?' . http_build_query($bookParams);
            ?>
            <article class="service-card" data-name="<?= htmlspecialchars($row['service_name']) ?>">
              <div class="service-card-head">
                <h3><?= htmlspecialchars($row['service_name']) ?></h3>
                <span class="duration-badge">
                  <i class="fa-regular fa-clock"></i>
                  <?= htmlspecialchars((string)($row['base_duration_minutes'] ?? 60)) ?> mins
                </span>
              </div>

              <p class="description"><?= htmlspecialchars($row['description'] ?? 'Professional service for your vehicle.') ?></p>

              <div class="price-row">
                <span class="price-label">Starting at</span>
                <strong class="price">Rs. <?= htmlspecialchars(number_format((float)$row['default_price'], 2)) ?></strong>
              </div>

              <a class="book-now" href="<?= htmlspecialchars($bookUrl) ?>">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Book Now</span>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <?php if (empty($services)): ?>
        <p class="muted">No services found for this branch.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer class="footer">© <?= date('Y') ?> AutoNexus — Drive Smarter.</footer>
  </div>

  <script src="<?= $base ?>/public/assets/js/customer/available-services.js?v=<?= (int)$availServicesJsVersion ?>"></script>
</body>
</html>
