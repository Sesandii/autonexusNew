<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background: #fff;
        }

        .invoice-box {
            max-width: 850px;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .right {
            text-align: right;
        }

        .total {
            margin-top: 25px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">

    <h2>Vehicle Service Invoice</h2>

    <div class="row">
        <div>
            <strong>Customer:</strong><br>
            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
            Phone: <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
        </div>

        <div>
            <strong>Vehicle:</strong><br>
            <?= htmlspecialchars($order['vehicle_no'] ?? $order['license_plate'] ?? 'N/A') ?><br>
            <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '')) ?><br>
            <?= htmlspecialchars(($order['year'] ?? '') . ' • ' . ($order['color'] ?? '')) ?>
        </div>
    </div>

    <p><strong>Work Order:</strong> #<?= $order['work_order_id'] ?></p>
    <p><strong>Date:</strong> <?= $order['completed_at'] ?></p>

    <table>
        <tr>
            <th>Description</th>
            <th class="right">Amount (Rs.)</th>
        </tr>
        <tr>
            <td><?= nl2br(htmlspecialchars($order['service_summary'] ?? 'Service Charges')) ?></td>
            <td class="right"><?= number_format($order['total_cost'], 2) ?></td>
        </tr>
    </table>

    <div class="total">
        Grand Total: Rs. <?= number_format($order['total_cost'], 2) ?>
    </div>

    <div class="no-print" style="margin-top:30px; text-align:center;">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

</div>

</body>
</html>
