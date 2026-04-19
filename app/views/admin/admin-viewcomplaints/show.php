<?php /* Admin view: renders admin-viewcomplaints/show page. */ ?>
<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$r = $record;
include 'helpers.php';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function fieldRow(string $icon, string $label, string $value): string
{
  return "<div class=\"field-row\">\n"
    . "  <div class=\"field-icon\"><i class=\"fa-solid {$icon}\"></i></div>\n"
    . "  <div class=\"field-content\">\n"
    . "    <div class=\"field-label\">{$label}</div>\n"
    . "    <div class=\"field-value\">{$value}</div>\n"
    . "  </div>\n"
    . "</div>";
}

$complaintId = '#' . (int) ($r['complaint_id'] ?? 0);
$subject = (string) ($r['subject'] ?? '-');
$status = (string) ($r['status'] ?? 'open');
$priorityLevel = (string) ($r['priority'] ?? 'medium');
$resolvedAt = trim((string) ($r['resolved_at'] ?? '')) ?: '—';
$customerName = trim((string) ($r['customer_name'] ?? '')) ?: 'Unknown Customer';
$customerCode = trim((string) ($r['customer_code'] ?? '')) ?: '—';
$description = trim((string) ($r['description'] ?? '')) ?: 'No description provided.';

$statusClass = in_array(strtolower($status), ['resolved', 'closed'], true) ? 'active' : 'inactive';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaint <?= e($complaintId) ?> Details</title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/show.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content branch-show-main branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Complaint Details</h1>
          <p><?= e($complaintId) ?> - <?= e($subject) ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/admin-viewcomplaints') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Complaints</span>
          </a>
        </div>
      </header>

      <div class="grid-three">
        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-label">Status</div>
          <div class="status-wrap"><span
              class="status-badge <?= e($statusClass) ?>"><?= e(ucwords(str_replace('_', ' ', $status))) ?></span></div>
        </div>
      </div>

      <div class="grid-two" style="align-items:start;">
        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-circle-info"></i>
            <h3>Complaint Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-hashtag', 'Complaint ID', e($complaintId)) ?>
            <?= fieldRow('fa-heading', 'Subject', e($subject)) ?>
            <?= fieldRow('fa-flag', 'Priority', priority($priorityLevel)) ?>
            <?= fieldRow('fa-signal', 'Status', badge($status)) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e((string) ($r['created_at'] ?? '—'))) ?>
            <?= fieldRow('fa-arrows-rotate', 'Updated At', e((string) ($r['updated_at'] ?? '—'))) ?>
            <?= fieldRow('fa-circle-check', 'Resolved At', e($resolvedAt)) ?>
            <?= fieldRow('fa-user', 'Customer', e($customerName)) ?>
            <?= fieldRow('fa-id-card', 'Customer Code', e($customerCode)) ?>
            <?= fieldRow('fa-envelope', 'Customer Email', e((string) ($r['customer_email'] ?? '—'))) ?>
            <?= fieldRow('fa-phone', 'Customer Phone', e((string) ($r['customer_phone'] ?? '—'))) ?>
            <?= fieldRow('fa-building', 'Branch', e((string) ($r['branch_name'] ?? '—'))) ?>
            <?= fieldRow('fa-briefcase', 'Service', e((string) ($r['service_name'] ?? '—'))) ?>
            <?= fieldRow('fa-car', 'Vehicle', e(trim(((string) ($r['make'] ?? '')) . ' ' . ((string) ($r['model'] ?? ''))) ?: '—')) ?>
            <?= fieldRow('fa-align-left', 'Description', nl2br(e($description))) ?>
          </div>
        </div>

        <div class="detail-card" id="admin-handling">
          <div class="card-header">
            <i class="fa-solid fa-user-gear"></i>
            <h3>Admin Handling</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-user-check', 'Assigned To', assignedUserInfo($r)) ?>

            <form method="POST" action="<?= $B ?>/admin/admin-viewcomplaints/update">
              <input type="hidden" name="complaint_id" value="<?= (int) $r['complaint_id'] ?>">

              <div class="field" style="margin-top: 12px;">
                <label class="label" style="display:block;margin-bottom:6px;">Status</label>
                <select name="status" required>
                  <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($r['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label" style="display:block;margin-bottom:6px;">Priority</label>
                <select name="priority" required>
                  <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($r['priority'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label" style="display:block;margin-bottom:6px;">Assign To</label>
                <select name="assigned_to_user_id">
                  <option value="">Unassigned</option>
                  <?php foreach ($assignableUsers as $u): ?>
                    <option value="<?= (int) $u['user_id'] ?>" <?= ((string) ($r['assigned_to_user_id'] ?? '') === (string) $u['user_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['role']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label" style="display:block;margin-bottom:6px;">Admin / Resolution Note</label>
                <textarea name="resolution_note" rows="6"
                  placeholder="Add action taken, escalation reason, follow-up, or resolution note..."></textarea>
              </div>

              <div class="form-actions" style="justify-content:flex-end; margin-top:12px;">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                <?php if (!empty($r['can_reopen'])): ?>
                  <button type="submit" name="reopen" value="1" class="btn-secondary"><i
                      class="fa-solid fa-rotate-left"></i> Reopen</button>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>