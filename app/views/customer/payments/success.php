<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - AutoNexus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            padding: 40px;
        }

        .box {
            max-width: 680px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 14px;
            background: #111827;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Payment submitted successfully</h1>
        <p>Your payment was submitted to Stripe.</p>
        <p>The invoice has been updated from the Stripe callback when payment was confirmed.</p>

        <?php if (!empty($invoice)): ?>
            <p><strong>Invoice:</strong> <?= htmlspecialchars($invoice['invoice_no'] ?? '') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($invoice['status'] ?? '') ?></p>
        <?php endif; ?>

        <a class="btn" href="<?= $base ?>/customer/payments">Back to Payments</a>
    </div>
</body>

</html>