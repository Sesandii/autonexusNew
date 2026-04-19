<?php /* Admin view: renders admin-ongoingservices/index page. */ ?>
<?php
/** @var array  $workOrders */
/** @var array  $branches */
/** @var string $selectedDate */
/** @var string $currentDateText */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'progress';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? 'Ongoing Services') ?></title>

    <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
    <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
    <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/ongoingservices/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-ongoingservicesindex.css?v=1">
</head>

<body>
    <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

    <main class="main-content">
        <section class="management">
            <header class="management-header">
                <div>
                    <h2>Ongoing Services</h2>
                    <p class="management-subtitle"><?= htmlspecialchars($currentDateText) ?></p>
                </div>

                <div class="tools service-progress-tools">
                    <div class="tool-field">
                        <label for="branchFilter">Branch</label>
                        <select id="branchFilter" class="status-filter">
                            <option value="">All Branches</option>
                            <?php foreach ($branches as $b): ?>
                                <option value="<?= (int) $b['branch_id'] ?>">
                                    <?= htmlspecialchars($b['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="tool-field">
                        <label for="statusFilter">Status</label>
                        <select id="statusFilter" class="status-filter">
                            <option value="">All Status</option>
                            <option value="Received">Received</option>
                            <option value="In Service">In Service</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
            </header>

            <div class="cards" id="cardsContainer">
                <?php foreach ($workOrders as $w):
                    $dt = new DateTime($w['datetime']);
                    $timeDisplay = $dt->format('g:i A');
                    $time24 = $dt->format('H:i');

                    $uiStatus = $w['status_ui'];
                    $statusDb = $w['status_db'];

                    $badgeClass = $uiStatus === 'Received' ? 'received'
                        : ($uiStatus === 'In Service' ? 'in-service'
                            : 'completed');

                    $progressClass = $badgeClass;
                    ?>
                    <div class="card" data-branch-id="<?= (int) $w['branch_id'] ?>"
                        data-status="<?= htmlspecialchars($uiStatus) ?>" data-time="<?= $time24 ?>">
                        <div class="card-header">
                            <div>
                                <h2><?= htmlspecialchars($w['service']) ?></h2>
                                <div style="margin-top:4px;">
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($uiStatus) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="duration-pill">
                                <?= (int) $w['duration_minutes'] ?: 0 ?> min
                            </div>
                        </div>

                        <p><strong>Customer:</strong> <?= htmlspecialchars($w['customer']) ?></p>
                        <p><strong>Branch:</strong> <?= htmlspecialchars($w['branch']) ?></p>
                        <p><strong>Assigned to:</strong> <?= htmlspecialchars($w['mechanic']) ?></p>
                        <p><strong>Appointment Time:</strong> <?= htmlspecialchars($timeDisplay) ?></p>

                        <div class="progress-bar">
                            <div class="progress-steps">
                                <span
                                    class="step <?= in_array($statusDb, ['open', 'in_progress', 'completed'], true) ? 'active' : '' ?>">Received</span>
                                <span
                                    class="step <?= in_array($statusDb, ['in_progress', 'completed'], true) ? 'active' : '' ?>">In
                                    Service</span>
                                <span class="step <?= $statusDb === 'completed' ? 'active' : '' ?>">Completed</span>
                            </div>
                            <div class="bar">
                                <div class="progress <?= $progressClass ?>"></div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn-view"
                                data-url="<?= $B ?>/admin/admin-ongoingservices/show?id=<?= (int) $w['id'] ?>">
                                <i class="fa-regular fa-eye"></i>
                                View
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardsContainer = document.getElementById('cardsContainer');
            if (!cardsContainer) return;

            const cards = Array.from(cardsContainer.querySelectorAll('.card'));
            const branchFilter = document.getElementById('branchFilter');
            const statusFilter = document.getElementById('statusFilter');

            function applyFilters() {
                const branchVal = branchFilter ? branchFilter.value : '';
                const statusVal = statusFilter ? statusFilter.value : '';

                cards.forEach(card => {
                    const cardBranch = card.dataset.branchId || '';
                    const cardStatus = card.dataset.status || '';

                    const matchesBranch = !branchVal || cardBranch === branchVal;
                    const matchesStatus = !statusVal || cardStatus === statusVal;

                    const visible = matchesBranch && matchesStatus;
                    card.style.display = visible ? '' : 'none';
                });
            }

            if (branchFilter) branchFilter.addEventListener('change', applyFilters);
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);

            // View button navigation
            cardsContainer.addEventListener('click', e => {
                const btn = e.target.closest('.btn-view');
                if (!btn) return;
                const url = btn.dataset.url;
                if (url) window.location.href = url;
            });
        });
    </script>
</body>

</html>