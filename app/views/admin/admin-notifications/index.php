<?php
/** @var string $pageTitle */
/** @var array $recent */
/** @var array $templates */
/** @var array|null $flash */
$current = 'notifications';
$B = rtrim(BASE_URL,'/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'AutoNexus - Notifications') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/notifications/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .main-content{margin-left:260px;padding:24px;background:#f4f5f7;min-height:100vh;}
    .card{background:#fff;border-radius:16px;padding:16px;box-shadow:0 1px 4px rgba(15,23,42,.08);margin:14px 0;}
    .row{display:flex;gap:10px;flex-wrap:wrap;}
    .row > div{flex:1;min-width:260px;}
    input, select, textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:12px;}
    textarea{min-height:120px;resize:vertical;}
    .btn{border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer}
    .btn-primary{background:#111827;color:#fff;}
    .hint{font-size:12px;color:#6b7280;margin-top:6px;}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:14px;}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;}
    .b-sent{background:#dcfce7;color:#166534;}
    .b-failed{background:#fee2e2;color:#991b1b;}
    .b-queued{background:#e0e7ff;color:#1e3a8a;}
    .flash{padding:10px 12px;border-radius:12px;margin:10px 0;font-weight:700;}
    .flash.success{background:#dcfce7;color:#166534;}
    .flash.warn{background:#fef9c3;color:#854d0e;}
    .flash.error{background:#fee2e2;color:#991b1b;}

    .pill{display:inline-flex;align-items:center;gap:8px;border:1px solid #d1d5db;padding:8px 10px;border-radius:999px;margin:6px 6px 0 0;background:#fff;}
    .pill button{border:0;background:transparent;cursor:pointer;font-weight:900;}
    .picker-results{border:1px solid #e5e7eb;border-radius:12px;padding:10px;max-height:220px;overflow:auto;}
    .picker-item{display:flex;justify-content:space-between;gap:10px;padding:8px;border-bottom:1px solid #f1f5f9;}
    .picker-item:last-child{border-bottom:0;}
    .picker-item button{padding:6px 10px;border-radius:10px;border:1px solid #d1d5db;background:#fff;cursor:pointer;}
  </style>
</head>
<body>

<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h2>Notifications</h2>

  <?php if (!empty($flash)): ?>
    <div class="flash <?= htmlspecialchars($flash['type'] ?? 'success') ?>">
      <?= htmlspecialchars($flash['text'] ?? '') ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <h3 style="margin-top:0;">Compose Notification (Email)</h3>

    <form method="post" action="<?= $B ?>/admin/admin-notifications/send">
      <div class="row">
        <div>
          <label><b>Template (Optional)</b></label>
          <select name="template_key" id="template_key">
            <option value="">— No template (custom) —</option>
            <?php foreach (($templates ?? []) as $t): ?>
              <option value="<?= htmlspecialchars($t['template_key']) ?>">
                <?= htmlspecialchars($t['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="hint">Use templates for: Promotion, Holiday hours, Branch closure, New service.</div>

          <div style="margin-top:10px;">
            <label><b>Details (used by template as {{details}})</b></label>
            <textarea name="details" placeholder="Example: 20% off Brake Pads until Feb 28"></textarea>
          </div>

          <div style="margin-top:10px;">
            <label><b>Recipients</b></label>
            <select name="audience" id="audience">
              <option value="all_customers">All Customers</option>
              <option value="upcoming_appointments">Customers with Upcoming Appointments</option>
              <option value="recent_customers">Recent Customers</option>
              <option value="selected_users">Select Customers from List</option>
              <option value="custom">Custom Emails</option>
            </select>

            <div id="upcomingBox" style="display:none;margin-top:10px;">
              <label>Upcoming window (days)</label>
              <input type="number" name="upcoming_days" value="1" min="1" max="30">
            </div>

            <div id="recentBox" style="display:none;margin-top:10px;">
              <label>Recent window (days)</label>
              <input type="number" name="recent_days" value="30" min="1" max="365">
            </div>

            <div id="customBox" style="display:none;margin-top:10px;">
              <label>Custom emails (comma/newline separated)</label>
              <textarea name="custom_emails" placeholder="a@gmail.com, b@gmail.com"></textarea>
            </div>

            <div id="selectBox" style="display:none;margin-top:10px;">
              <label>Search customers</label>
              <input type="text" id="userSearch" placeholder="Type name or email...">
              <div class="hint">Click “Add” to include recipients.</div>

              <div id="pickerResults" class="picker-results" style="margin-top:8px;"></div>

              <div id="pickedUsers" style="margin-top:8px;"></div>
              <div id="pickedInputs"></div>
            </div>
          </div>
        </div>

        <div>
          <label><b>Subject</b></label>
          <input type="text" name="subject" placeholder="Enter subject (or leave blank to use template)" >

          <div style="margin-top:10px;">
            <label><b>Message</b></label>
            <textarea name="message" placeholder="Write your message (or leave blank to use template)"></textarea>
            <div class="hint">Tip: Keep it short + clear. Add date/time, branch name, contact number.</div>
          </div>

          <button type="submit" class="btn btn-primary" style="margin-top:10px;">
            <i class="fas fa-paper-plane"></i> Send Notification
          </button>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">Recent Notifications</h3>

    <table>
      <thead>
        <tr>
          <th>Subject</th>
          <th>Audience</th>
          <th>Status</th>
          <th>Total</th>
          <th>Sent</th>
          <th>Failed</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recent)): ?>
          <tr><td colspan="7">No notifications yet.</td></tr>
        <?php else: ?>
          <?php foreach ($recent as $n): ?>
            <?php
              $st = (string)$n['status'];
              $badgeClass = $st === 'sent' ? 'b-sent' : ($st === 'failed' ? 'b-failed' : 'b-queued');
            ?>
            <tr>
              <td><?= htmlspecialchars($n['subject']) ?></td>
              <td><?= htmlspecialchars(str_replace('_',' ', (string)$n['audience'])) ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($st) ?></span></td>
              <td><?= (int)$n['recipients_total'] ?></td>
              <td><?= (int)$n['recipients_sent'] ?></td>
              <td><?= (int)$n['recipients_failed'] ?></td>
              <td><?= htmlspecialchars((string)$n['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
  const BASE = "<?= $B ?>";

  const audience = document.getElementById('audience');
  const upcomingBox = document.getElementById('upcomingBox');
  const recentBox = document.getElementById('recentBox');
  const customBox = document.getElementById('customBox');
  const selectBox = document.getElementById('selectBox');

  function toggleBoxes() {
    const v = audience.value;
    upcomingBox.style.display = (v === 'upcoming_appointments') ? 'block' : 'none';
    recentBox.style.display   = (v === 'recent_customers') ? 'block' : 'none';
    customBox.style.display   = (v === 'custom') ? 'block' : 'none';
    selectBox.style.display   = (v === 'selected_users') ? 'block' : 'none';
  }
  audience.addEventListener('change', toggleBoxes);
  toggleBoxes();

  // ---- Recipient picker (selected_users) ----
  const userSearch = document.getElementById('userSearch');
  const pickerResults = document.getElementById('pickerResults');
  const pickedUsers = document.getElementById('pickedUsers');
  const pickedInputs = document.getElementById('pickedInputs');

  const picked = new Map(); // user_id -> {name,email}

  function renderPicked() {
    pickedUsers.innerHTML = '';
    pickedInputs.innerHTML = '';
    for (const [id, u] of picked.entries()) {
      const pill = document.createElement('span');
      pill.className = 'pill';
      pill.innerHTML = `${u.name} (${u.email}) <button type="button" data-id="${id}">×</button>`;
      pickedUsers.appendChild(pill);

      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'selected_users[]';
      inp.value = id;
      pickedInputs.appendChild(inp);
    }

    pickedUsers.querySelectorAll('button[data-id]').forEach(btn => {
      btn.addEventListener('click', () => {
        picked.delete(btn.dataset.id);
        renderPicked();
      });
    });
  }

  let t = null;
  async function searchUsers(q) {
    const res = await fetch(`${BASE}/admin/admin-notifications/users?q=${encodeURIComponent(q)}`);
    return res.json();
  }

  function renderResults(rows) {
    pickerResults.innerHTML = '';
    if (!rows.length) {
      pickerResults.innerHTML = '<div class="hint">No matches.</div>';
      return;
    }

    rows.forEach(r => {
      const name = `${r.first_name} ${r.last_name}`.trim();
      const div = document.createElement('div');
      div.className = 'picker-item';
      div.innerHTML = `
        <div>
          <div><b>${name}</b></div>
          <div class="hint">${r.email}</div>
        </div>
        <div>
          <button type="button">Add</button>
        </div>
      `;
      div.querySelector('button').addEventListener('click', () => {
        picked.set(String(r.user_id), {name, email: r.email});
        renderPicked();
      });
      pickerResults.appendChild(div);
    });
  }

  if (userSearch) {
    userSearch.addEventListener('input', () => {
      clearTimeout(t);
      const q = userSearch.value.trim();
      t = setTimeout(async () => {
        if (!q) { pickerResults.innerHTML = ''; return; }
        const rows = await searchUsers(q);
        renderResults(rows);
      }, 250);
    });
  }
</script>

</body>
</html>
