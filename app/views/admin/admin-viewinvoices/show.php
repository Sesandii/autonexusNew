<?php $B = rtrim(BASE_URL,'/'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></title>
</head>
<body>

<h2>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></h2>

<p><strong>Customer:</strong>
<?= htmlspecialchars($invoice['first_name'].' '.$invoice['last_name']) ?></p>

<p><strong>Email:</strong> <?= htmlspecialchars($invoice['email']) ?></p>
<p><strong>Vehicle:</strong>
<?= htmlspecialchars($invoice['make'].' '.$invoice['model'].' ('.$invoice['license_plate'].')') ?></p>

<p><strong>Service:</strong> <?= htmlspecialchars($invoice['service_name']) ?></p>
<p><strong>Total:</strong> Rs.<?= number_format($invoice['grand_total'],2) ?></p>
<p><strong>Status:</strong> <?= ucfirst($invoice['status']) ?></p>

<a href="<?= $B ?>/admin/admin-viewinvoices/download?id=<?= (int)$invoice['invoice_id'] ?>">
  Download PDF
</a>

</body>
</html>
