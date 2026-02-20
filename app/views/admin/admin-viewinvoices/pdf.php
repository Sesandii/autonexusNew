<!DOCTYPE html>
<html>
<head>
<style>
body {
  font-family: DejaVu Sans, sans-serif;
  font-size: 13px;
  color: #111;
}
h1 {
  text-align: center;
  margin-bottom: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
td, th {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
.total {
  text-align: right;
  font-size: 15px;
  font-weight: bold;
}
.header {
  margin-bottom: 20px;
}
.small {
  font-size: 12px;
  color: #555;
}
</style>
</head>

<body>

<h1>AutoNexus Invoice</h1>

<div class="header">
  <p><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_no']) ?></p>
  <p><strong>Date:</strong> <?= date('M d, Y', strtotime($invoice['issued_at'])) ?></p>
</div>

<table>
<tr>
  <td><strong>Customer</strong></td>
  <td><?= htmlspecialchars($invoice['first_name'].' '.$invoice['last_name']) ?></td>
</tr>
<tr>
  <td><strong>Email</strong></td>
  <td><?= htmlspecialchars($invoice['email']) ?></td>
</tr>
<tr>
  <td><strong>Vehicle</strong></td>
  <td>
    <?= htmlspecialchars(
      $invoice['make'].' '.$invoice['model'].
      ' ('.$invoice['license_plate'].')'
    ) ?>
  </td>
</tr>
<tr>
  <td><strong>Service</strong></td>
  <td><?= htmlspecialchars($invoice['service_name']) ?></td>
</tr>
</table>

<br>

<table>
<tr>
  <th>Description</th>
  <th align="right">Amount (Rs.)</th>
</tr>
<tr>
  <td><?= htmlspecialchars($invoice['service_summary'] ?? 'Service Charges') ?></td>
  <td align="right"><?= number_format($invoice['total_amount'],2) ?></td>
</tr>
<tr>
  <td>Discount</td>
  <td align="right">-<?= number_format($invoice['discount'],2) ?></td>
</tr>
<tr>
  <td class="total">Grand Total</td>
  <td class="total" align="right">
    <?= number_format($invoice['grand_total'],2) ?>
  </td>
</tr>
</table>

<p class="small">
  Status: <?= strtoupper($invoice['status']) ?>
</p>

<p class="small" style="margin-top:40px;">
  Thank you for choosing AutoNexus.
</p>

</body>
</html>
