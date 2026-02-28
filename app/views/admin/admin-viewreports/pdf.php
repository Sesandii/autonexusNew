<?php
/** @var string $pdfTitle */
/** @var string $generatedAt */
/** @var string $key */
/** @var array  $filters */
/** @var array  $rows */
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
    h1 { font-size: 18px; margin: 0 0 8px; }
    .meta { margin-bottom: 12px; color: #444; font-size: 11px; }
    .meta div { margin: 2px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f3f4f6; text-align: left; }
    .right { text-align: right; }
    .small { font-size: 11px; color: #555; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($pdfTitle) ?></h1>

  <div class="meta">
    <div><strong>Generated:</strong> <?= htmlspecialchars($generatedAt) ?></div>
    <div><strong>From:</strong> <?= htmlspecialchars($filters['from'] ?? '') ?> &nbsp; <strong>To:</strong> <?= htmlspecialchars($filters['to'] ?? '') ?></div>
    <div><strong>Branch ID:</strong> <?= htmlspecialchars((string)($filters['branch_id'] ?? '')) ?> &nbsp; <strong>Group:</strong> <?= htmlspecialchars($filters['group'] ?? '') ?></div>
    <div class="small"><strong>Dataset key:</strong> <?= htmlspecialchars($key) ?></div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:70%;">Label</th>
        <th style="width:30%;" class="right">Value</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="2">No data for selected filters.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars((string)($r['label'] ?? '')) ?></td>
            <td class="right"><?= htmlspecialchars((string)($r['value'] ?? '0')) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
