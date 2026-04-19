<?php /* Admin view: renders admin-viewservices/edit page. */ ?>
<?php $current = 'services';
$base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Service / Package</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/services/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content services-form-page">
    <header class="page-head">
      <div>
        <h1>Edit Service</h1>
        <p class="muted">Update details, package composition, pricing, and branch availability.</p>
      </div>
    </header>

    <form method="post" action="<?= $base ?>/admin/services/<?= (int) $row['service_id'] ?>">
      <div class="card">
        <div class="grid">
          <div class="field">
            <label>Service Code</label>
            <input type="text" value="<?= htmlspecialchars($row['service_code']) ?>" readonly>
          </div>

          <div class="field">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
          </div>

          <div class="field">
            <label for="type_id">Category</label>
            <select id="type_id" name="type_id" required>
              <option value="">-- Select Type --</option>
              <?php foreach (($types ?? []) as $t): ?>
                <option value="<?= (int) $t['type_id'] ?>" <?= (int) $row['type_id'] === (int) $t['type_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($t['type_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
              <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="rejected" <?= $row['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
          </div>

          <div class="field">
            <label for="base_duration_minutes">Duration (minutes)</label>
            <input type="number" id="base_duration_minutes" name="base_duration_minutes" min="0"
              value="<?= (int) $row['base_duration_minutes'] ?>">
          </div>

          <div class="field">
            <label for="default_price">Price</label>
            <input type="number" id="default_price" name="default_price" min="0" step="0.01"
              value="<?= htmlspecialchars((string) $row['default_price']) ?>">
          </div>
        </div>

        <div class="field form-textarea">
          <label for="description">Description</label>
          <textarea id="description" name="description"
            rows="4"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="card is-hidden" id="packageBuilder">
        <h2>Package Builder</h2>

        <div id="packageItemsWrap"></div>

        <button type="button" class="btn btn-secondary" id="addPackageItemBtn">
          <i class="fa-solid fa-plus"></i> Add package item
        </button>

        <div class="grid">
          <div class="field">
            <label for="pricing_mode">Pricing Mode</label>
            <select id="pricing_mode" name="pricing_mode">
              <option value="auto">Auto from items</option>
              <option value="manual">Manual final price</option>
            </select>
          </div>

          <div class="field">
            <label for="discount_type">Discount Type</label>
            <select id="discount_type" name="discount_type">
              <option value="none">None</option>
              <option value="fixed">Fixed amount</option>
              <option value="percent">Percentage</option>
            </select>
          </div>

          <div class="field">
            <label for="discount_value">Discount Value</label>
            <input type="number" id="discount_value" name="discount_value" min="0" step="0.01" value="0">
          </div>

          <div class="field">
            <label for="manual_price">Manual Final Price</label>
            <input type="number" id="manual_price" name="manual_price" min="0" step="0.01" value="">
          </div>
        </div>

        <div class="summary">
          <div class="metric">
            <div class="label">Items base total</div>
            <div class="value" id="baseTotalLabel">Rs. 0.00</div>
          </div>
          <div class="metric">
            <div class="label">Calculated duration</div>
            <div class="value" id="durationLabel">0 min</div>
          </div>
          <div class="metric">
            <div class="label">Final package price</div>
            <div class="value" id="finalPriceLabel">Rs. 0.00</div>
          </div>
          <div class="metric">
            <div class="label">Package items</div>
            <div class="value" id="itemCountLabel">0</div>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Branch Availability</h2>

        <div class="field form-field-stack">
          <div class="field-choices">
            <label><input type="radio" name="apply_scope" value="all" <?= !empty($applyAll) ? 'checked' : '' ?>> Apply to
              all active branches</label>
            <label><input type="radio" name="apply_scope" value="specific" <?= empty($applyAll) ? 'checked' : '' ?>>
              Apply to specific branches</label>
          </div>
        </div>

        <div id="branchPicker" class="branch-box <?= !empty($applyAll) ? 'is-hidden' : '' ?>">
          <?php
          $attachedSet = array_flip($attached ?? []);
          foreach (($branches ?? []) as $b):
            $bid = (int) $b['branch_id'];
            ?>
            <label class="field-check">
              <input type="checkbox" name="branches[]" value="<?= $bid ?>" <?= isset($attachedSet[$bid]) ? 'checked' : '' ?>>
              <span><?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['branch_code']) ?>)</span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="form-actions">
        <a class="btn btn-secondary" href="<?= $base ?>/admin/admin-viewservices">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </main>

  <script>
    const packageTypeId = <?= isset($packageTypeId) && $packageTypeId ? (int) $packageTypeId : 'null' ?>;
    const serviceOptions = <?= json_encode(array_values($servicesForPackage ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const existingItems = <?= json_encode(array_map(function ($item) {
      return [
        'service_id' => (int) $item['service_id'],
        'quantity' => (int) $item['quantity'],
      ];
    }, $packageItems ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    const typeSelect = document.getElementById('type_id');
    const packageBuilder = document.getElementById('packageBuilder');
    const itemsWrap = document.getElementById('packageItemsWrap');
    const addItemBtn = document.getElementById('addPackageItemBtn');
    const pricingMode = document.getElementById('pricing_mode');
    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const manualPrice = document.getElementById('manual_price');
    const durationInput = document.getElementById('base_duration_minutes');
    const priceInput = document.getElementById('default_price');
    const baseTotalLabel = document.getElementById('baseTotalLabel');
    const durationLabel = document.getElementById('durationLabel');
    const finalPriceLabel = document.getElementById('finalPriceLabel');
    const itemCountLabel = document.getElementById('itemCountLabel');

    const radios = document.querySelectorAll('input[name="apply_scope"]');
    const branchPicker = document.getElementById('branchPicker');

    function toggleBranches() {
      const selected = [...radios].find(r => r.checked)?.value;
      branchPicker.style.display = selected === 'specific' ? 'block' : 'none';
    }
    radios.forEach(r => r.addEventListener('change', toggleBranches));

    function isPackageSelected() {
      return packageTypeId !== null && parseInt(typeSelect.value || '0', 10) === packageTypeId;
    }

    function optionHtml(selectedId = '') {
      let html = '<option value="">-- Select service --</option>';
      for (const svc of serviceOptions) {
        const selected = String(selectedId) === String(svc.service_id) ? 'selected' : '';
        html += `<option value="${svc.service_id}" data-price="${svc.default_price}" data-duration="${svc.base_duration_minutes}" ${selected}>${svc.service_code} - ${svc.name}</option>`;
      }
      return html;
    }

    function addPackageRow(selectedId = '', qty = 1) {
      const index = itemsWrap.querySelectorAll('.pkg-row').length;

      const row = document.createElement('div');
      row.className = 'pkg-row';
      row.innerHTML = `
      <select name="package_items[${index}][service_id]" class="pkg-service">
        ${optionHtml(selectedId)}
      </select>
      <input type="number" name="package_items[${index}][quantity]" class="pkg-qty" min="1" value="${qty}">
      <button type="button" class="btn btn-secondary remove-row">Remove</button>
    `;

      row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        refreshPackageSummary();
      });

      row.querySelector('.pkg-service').addEventListener('change', refreshPackageSummary);
      row.querySelector('.pkg-qty').addEventListener('input', refreshPackageSummary);

      itemsWrap.appendChild(row);
      refreshPackageSummary();
    }

    function refreshPackageSummary() {
      if (!isPackageSelected()) {
        baseTotalLabel.textContent = 'Rs. 0.00';
        durationLabel.textContent = '0 min';
        finalPriceLabel.textContent = 'Rs. 0.00';
        itemCountLabel.textContent = '0';
        return;
      }

      const rows = itemsWrap.querySelectorAll('.pkg-row');
      let duration = 0;
      let total = 0;
      let count = 0;

      rows.forEach(row => {
        const select = row.querySelector('.pkg-service');
        const qtyEl = row.querySelector('.pkg-qty');
        const opt = select.options[select.selectedIndex];
        const qty = Math.max(1, parseInt(qtyEl.value || '1', 10));

        if (opt && opt.value) {
          const price = parseFloat(opt.dataset.price || '0');
          const mins = parseInt(opt.dataset.duration || '0', 10);

          total += price * qty;
          duration += mins * qty;
          count++;
        }
      });

      let finalPrice = total;
      const discountVal = parseFloat(discountValue.value || '0');

      if (pricingMode.value === 'manual' && manualPrice.value !== '') {
        finalPrice = Math.max(0, parseFloat(manualPrice.value || '0'));
      } else {
        if (discountType.value === 'fixed') {
          finalPrice = Math.max(0, total - discountVal);
        } else if (discountType.value === 'percent') {
          finalPrice = Math.max(0, total - (total * discountVal / 100));
        }
      }

      durationInput.value = duration;
      priceInput.value = finalPrice.toFixed(2);

      baseTotalLabel.textContent = 'Rs. ' + total.toFixed(2);
      durationLabel.textContent = duration + ' min';
      finalPriceLabel.textContent = 'Rs. ' + finalPrice.toFixed(2);
      itemCountLabel.textContent = count;
    }

    function togglePackageBuilder() {
      packageBuilder.style.display = isPackageSelected() ? 'block' : 'none';
    }

    addItemBtn.addEventListener('click', () => addPackageRow());
    pricingMode.addEventListener('change', refreshPackageSummary);
    discountType.addEventListener('change', refreshPackageSummary);
    discountValue.addEventListener('input', refreshPackageSummary);
    manualPrice.addEventListener('input', refreshPackageSummary);
    typeSelect.addEventListener('change', () => {
      togglePackageBuilder();
      refreshPackageSummary();
    });

    if (existingItems.length > 0) {
      existingItems.forEach(item => addPackageRow(item.service_id, item.quantity));
    } else if (isPackageSelected()) {
      addPackageRow();
    }

    togglePackageBuilder();
    toggleBranches();
    if (isPackageSelected()) {
      refreshPackageSummary();
    }
  </script>
</body>

</html>