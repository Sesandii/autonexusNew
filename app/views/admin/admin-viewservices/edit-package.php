<?php /* Admin view: renders admin-viewservices/edit-package page. */ ?>
<?php $current = 'services';
$base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package</title>

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
                <h1>Edit Package</h1>
                <p class="muted">Update package details, items, pricing, and branch availability.</p>
            </div>
        </header>

        <form method="post" action="<?= $base ?>/admin/packages/<?= (int) $row['service_id'] ?>">
            <div class="card">
                <div class="grid">
                    <div class="field">
                        <label>Package Code</label>
                        <input type="text" value="<?= htmlspecialchars($packageCode ?? $row['service_code']) ?>"
                            readonly>
                    </div>

                    <div class="field">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive
                            </option>
                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="rejected" <?= $row['status'] === 'rejected' ? 'selected' : '' ?>>Rejected
                            </option>
                        </select>
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

            <div class="card">
                <h2>Package Items</h2>

                <div id="packageItemsWrap"></div>

                <button type="button" class="btn btn-secondary" id="addPackageItemBtn">
                    <i class="fa-solid fa-plus"></i> Add service
                </button>

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
                <h2>Pricing</h2>
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
            </div>

            <div class="card">
                <h2>Branch Availability</h2>

                <div class="field form-field-stack">
                    <div class="field-choices">
                        <label><input type="radio" name="apply_scope" value="all" <?= ($applyAll ?? false) ? 'checked' : '' ?>> Apply to all active branches</label>
                        <label><input type="radio" name="apply_scope" value="specific" <?= !($applyAll ?? false) ? 'checked' : '' ?>> Apply to specific branches</label>
                    </div>
                </div>

                <div id="branchPicker" class="branch-box <?= ($applyAll ?? false) ? 'is-hidden' : '' ?>">
                    <?php foreach (($branches ?? []) as $b): ?>
                        <label class="field-check">
                            <input type="checkbox" name="branches[]" value="<?= (int) $b['branch_id'] ?>"
                                <?= in_array((int) $b['branch_id'], $attached ?? []) ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['branch_code']) ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?= $base ?>/admin/admin-viewservices">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Package</button>
            </div>
        </form>
    </main>

    <script>
        const serviceOptions = <?= json_encode(array_values($servicesForPackage ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const currentItems = <?= json_encode($packageItems ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        const itemsWrap = document.getElementById('packageItemsWrap');
        const addItemBtn = document.getElementById('addPackageItemBtn');
        const pricingMode = document.getElementById('pricing_mode');
        const discountType = document.getElementById('discount_type');
        const discountValue = document.getElementById('discount_value');
        const manualPrice = document.getElementById('manual_price');
        const priceInput = document.getElementById('default_price');
        const baseTotalLabel = document.getElementById('baseTotalLabel');
        const durationLabel = document.getElementById('durationLabel');
        const finalPriceLabel = document.getElementById('finalPriceLabel');
        const itemCountLabel = document.getElementById('itemCountLabel');

        const radios = document.querySelectorAll('input[name="apply_scope"]');
        const branchPicker = document.getElementById('branchPicker');

        function toggleBranches() {
            const useSpecific = document.querySelector('input[name="apply_scope"]:checked').value === 'specific';
            branchPicker.style.display = useSpecific ? 'block' : 'none';
        }

        radios.forEach(r => r.addEventListener('change', toggleBranches));

        function buildSelectOptions() {
            return serviceOptions.map(s =>
                `<option value="${s.service_id}">${s.service_code} - ${s.name}</option>`
            ).join('');
        }

        function addPackageItem(serviceId = null, qty = 1) {
            const idx = itemsWrap.querySelectorAll('.pkg-row').length;
            const html = `
      <div class="pkg-row" data-idx="${idx}">
        <select name="package_items[${idx}][service_id]" required>
          <option value="">-- Select service --</option>
          ${buildSelectOptions()}
        </select>
        <input type="number" name="package_items[${idx}][quantity]" min="1" value="${qty}" required>
        <button type="button" class="btn btn-secondary" onclick="this.parentElement.remove(); updateSummary();">Remove</button>
      </div>
    `;
            itemsWrap.insertAdjacentHTML('beforeend', html);
            if (serviceId) {
                itemsWrap.querySelector(`[data-idx="${idx}"] select`).value = serviceId;
            }
            updateSummary();
        }

        function updateSummary() {
            const items = Array.from(itemsWrap.querySelectorAll('.pkg-row')).map(row => {
                const serviceId = parseInt(row.querySelector('select').value) || 0;
                const qty = parseInt(row.querySelector('input[type="number"]').value) || 1;
                return { serviceId, qty };
            });

            const baseTotal = items.reduce((sum, item) => {
                const service = serviceOptions.find(s => s.service_id === item.serviceId);
                return sum + (service ? service.default_price * item.qty : 0);
            }, 0);

            const duration = items.reduce((sum, item) => {
                const service = serviceOptions.find(s => s.service_id === item.serviceId);
                return sum + (service ? service.base_duration_minutes * item.qty : 0);
            }, 0);

            let finalPrice = baseTotal;
            if (discountType.value === 'fixed') {
                finalPrice -= parseFloat(discountValue.value) || 0;
            } else if (discountType.value === 'percent') {
                finalPrice -= baseTotal * ((parseFloat(discountValue.value) || 0) / 100);
            }

            if (pricingMode.value === 'manual' && manualPrice.value) {
                finalPrice = parseFloat(manualPrice.value) || 0;
            }

            baseTotalLabel.textContent = `Rs. ${baseTotal.toFixed(2)}`;
            durationLabel.textContent = `${duration} min`;
            finalPriceLabel.textContent = `Rs. ${Math.max(0, finalPrice).toFixed(2)}`;
            itemCountLabel.textContent = items.length;
            priceInput.value = Math.max(0, finalPrice).toFixed(2);
        }

        // Load existing items
        currentItems.forEach(item => {
            addPackageItem(item.service_id, item.quantity);
        });

        addItemBtn.addEventListener('click', () => addPackageItem());
        pricingMode.addEventListener('change', updateSummary);
        discountType.addEventListener('change', updateSummary);
        discountValue.addEventListener('input', updateSummary);
        manualPrice.addEventListener('input', updateSummary);

        // Initial update
        updateSummary();
    </script>
</body>

</html>