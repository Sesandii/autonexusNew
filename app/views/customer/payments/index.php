<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My Payments') ?> - AutoNexus</title>
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f7f7f9; margin:0; }
        .page { margin-left: 260px; padding: 24px; }
        .card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,.06); }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
        .badge { padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .paid { background:#dcfce7; color:#166534; }
        .unpaid { background:#fee2e2; color:#991b1b; }
        .btn { display:inline-block; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:600; }
        .btn-pay { background:#635bff; color:white; }
        .btn-disabled { background:#d1d5db; color:#6b7280; pointer-events:none; }
        @media (max-width: 900px) { .page { margin-left:0; } }
    </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

<div class="page">
    <div class="card">
        <h1><i class="fa-solid fa-credit-card"></i> My Payments</h1>
        <p>View your invoices and pay unpaid invoices online.</p>

        <?php if (empty($invoices)): ?>
            <p>No invoices found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Vehicle</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <?php $status = $inv['invoice_status'] ?? $inv['status'] ?? 'unpaid'; ?>
                        <tr>
                            <td><?= htmlspecialchars($inv['invoice_no']) ?></td>
                            <td><?= htmlspecialchars($inv['license_plate'] . ' (' . $inv['make'] . ' ' . $inv['model'] . ')') ?></td>
                            <td><?= htmlspecialchars($inv['service_name'] ?? 'Service') ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($inv['issued_at']))) ?></td>
                            <td>Rs. <?= number_format((float)$inv['grand_total'], 2) ?></td>
                            <td>
                                <?php if ($status === 'paid'): ?>
                                    <span class="badge paid">Paid</span>
                                <?php else: ?>
                                    <span class="badge unpaid">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($status === 'unpaid'): ?>
                                    <a class="btn btn-pay" href="<?= $base ?>/customer/payments/checkout/<?= (int)$inv['invoice_id'] ?>">
                                        Pay Online
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-disabled">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>