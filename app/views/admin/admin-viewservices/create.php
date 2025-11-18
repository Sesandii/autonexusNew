<?php $current = 'services'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Service</title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/services/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:20px}
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{font-weight:600}
    .branch-box{max-height:260px;overflow:auto;border:1px solid #e5e7eb;border-radius:10px;padding:10px}
    .actions{display:flex;gap:10px;margin-top:16px}
    .btn{padding:10px 16px;border-radius:8px;border:1px solid transparent;cursor:pointer}
    .btn-primary{background:#dc2626;color:#fff}
    .btn-primary:hover{background: #ef4444;}
    .btn-secondary{background:#f3f4f6}
    .hint{font-size:12px;color:#6b7280}
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <h1 class="admin-title">Create Service</h1>
    <p class="subtitle">Define the service and choose which branches it’s available in.</p>

    <form method="post" action="<?= rtrim(BASE_URL,'/') ?>/admin/services">
      <div class="card">
        <div class="grid-2">
          <div class="field">
           <div class="field">
  <label for="service_code">Service Code</label>
  <input type="text" id="service_code" name="service_code"
         value="<?= htmlspecialchars($nextCode ?? '', ENT_QUOTES) ?>"
         readonly>
  <span class="hint">Auto-generated (SER001, SER002, ...)</span>
</div>


          <div class="field">
            <label for="name">Service Name</label>
            <input type="text" id="name" name="name" required placeholder="e.g. Standard Oil Change">
          </div>

          <div class="field">
            <label for="type_id">Category</label>
            <select id="type_id" name="type_id" required>
              <option value="">-- Select Type --</option>
              <?php foreach (($types ?? []) as $t): ?>
                <option value="<?= (int)$t['type_id'] ?>"><?= htmlspecialchars($t['type_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
              <option value="active" selected>Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="field">
            <label for="base_duration_minutes">Base Duration (minutes)</label>
            <input type="number" id="base_duration_minutes" name="base_duration_minutes" min="0" step="1" placeholder="e.g. 45">
          </div>

          <div class="field">
            <label for="default_price">Default Price</label>
            <input type="number" id="default_price" name="default_price" min="0" step="0.01" placeholder="e.g. 4500.00">
          </div>
        </div>

        <!-- BEGIN: Package picker (hidden unless Category = Package) -->
<div class="field" id="package-services-wrap" style="display:none;">
  <label for="package_services">Services in this Package</label>
  <select id="package_services" name="package_services[]" multiple size="6">
    <?php foreach (($servicesForPackage ?? []) as $svc): ?>
      <option value="<?= (int)$svc['service_id'] ?>">
        <?= htmlspecialchars($svc['service_code'].' — '.$svc['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <span class="hint">Hold Ctrl/Cmd to select multiple. Leave empty if not a package.</span>
</div>
<!-- END: Package picker -->


        <div class="field" style="margin-top:12px;">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="4" placeholder="Short description..."></textarea>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-top:0">Branch Availability</h3>
        <div class="field">
          <label><input type="radio" name="apply_scope" value="all" checked> Apply to <b>all branches</b></label>
          <label><input type="radio" name="apply_scope" value="specific"> Apply to <b>specific branches</b></label>
        </div>

        <div id="branch-picker" class="branch-box" style="display:none;">
          <?php foreach (($branches ?? []) as $b): ?>
            <label style="display:flex;gap:8px;align-items:center;margin-bottom:6px;">
              <input type="checkbox" name="branches[]" value="<?= (int)$b['branch_id'] ?>">
              <span><?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['branch_code']) ?>)</span>
            </label>
          <?php endforeach; ?>
        </div>
        <span class="hint">If you choose “specific”, tick one or more branches.</span>
      </div>

      <div class="actions">
        <a class="btn btn-secondary" href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewservices">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Service</button>
      </div>
    </form>
  </main>

  <script>
    const radios = document.querySelectorAll('input[name="apply_scope"]');
    const box    = document.getElementById('branch-picker');
    const update = () => {
      const val = [...radios].find(r=>r.checked)?.value;
      box.style.display = (val === 'specific') ? 'block' : 'none';
    };
    radios.forEach(r => r.addEventListener('change', update));
    update();
  </script>

  <script>
  const typeSel = document.getElementById('type_id');
  const pkgWrap = document.getElementById('package-services-wrap');

  function isPackageSelected() {
    const opt = typeSel.options[typeSel.selectedIndex];
    if (!opt) return false;
    // normalize & match “package” or “packages” (case-insensitive)
    const label = (opt.text || '').trim().toLowerCase();
    return /(^|\s)packages?($|\s)/i.test(label);
  }

  function togglePackageUI() {
    if (!pkgWrap) return; // safety for edit page if block not present yet
    pkgWrap.style.display = isPackageSelected() ? 'block' : 'none';
  }

  typeSel.addEventListener('change', togglePackageUI);
  togglePackageUI(); // run once on load
</script>


</body>
</html>
