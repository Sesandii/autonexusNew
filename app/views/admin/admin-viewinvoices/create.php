<?php $B = rtrim(BASE_URL,'/'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Create Invoice</title>
  <style>
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid #ddd}
    .btn{padding:6px 12px;background:#111;color:#fff;border-radius:6px}
    .hidden{display:none}
  </style>
</head>
<body>

<h2>Create Invoice</h2>

<table>
<thead>
<tr>
  <th>Customer</th>
  <th>Service</th>
  <th>Completed</th>
  <th>Total</th>
  <th></th>
</tr>
</thead>
<tbody>
<?php foreach ($workOrders as $wo): ?>
<tr>
  <td><?= htmlspecialchars($wo['first_name'].' '.$wo['last_name']) ?></td>
  <td><?= htmlspecialchars($wo['service_name']) ?></td>
  <td><?= date('M d, Y', strtotime($wo['completed_at'])) ?></td>
  <td>Rs.<?= number_format($wo['total_cost'],2) ?></td>
  <td>
    <button class="btn"
      onclick="openForm(
        <?= (int)$wo['work_order_id'] ?>,
        <?= (float)$wo['total_cost'] ?>
      )">
      Create Invoice
    </button>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>

<form method="post"
      action="<?= $B ?>/admin/admin-viewinvoices/store"
      id="invoiceForm"
      class="hidden">

  <input type="hidden" name="work_order_id" id="workOrderId">

  <label>Total Amount</label>
  <input type="number" name="total_amount" id="totalAmount" readonly>

  <label>Discount</label>
  <input type="number" name="discount" id="discount" value="0">

  <label>Grand Total</label>
  <input type="number" name="grand_total" id="grandTotal" readonly>

  <button class="btn" type="submit">Generate Invoice</button>
</form>

<script>
function openForm(id, total) {
  document.getElementById('invoiceForm').classList.remove('hidden');
  document.getElementById('workOrderId').value = id;
  document.getElementById('totalAmount').value = total;
  document.getElementById('grandTotal').value = total;
}

document.getElementById('discount').addEventListener('input', e => {
  const total = parseFloat(document.getElementById('totalAmount').value);
  const discount = parseFloat(e.target.value || 0);
  document.getElementById('grandTotal').value = total - discount;
});
</script>

</body>
</html>
