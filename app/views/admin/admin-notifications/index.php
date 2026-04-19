<?php /* Admin view: renders admin-notifications/index page. */ ?>
<?php
/** @var string $pageTitle */
/** @var array $recent */
/** @var array $templates */
/** @var array|null $flash */
$current = 'notifications';
$B = rtrim(BASE_URL, '/');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'AutoNexus - Notifications') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/notifications/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>

  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content notifications-page">
    <section class="management">
      <header class="management-header">
        <div>
          <h2>Notifications</h2>
          <p class="management-subtitle">Create and track customer communication campaigns.</p>
        </div>
      </header>

      <?php if (!empty($flash) && !empty($flash['text'])): ?>
        <div class="flash <?= e((string) ($flash['type'] ?? 'success')) ?>">
          <i class="fa-solid fa-circle-info"></i>
          <span><?= e((string) $flash['text']) ?></span>
        </div>
      <?php endif; ?>

      <section class="compose-card" aria-label="Compose Notification">
        <div class="compose-head">
          <i class="fa-solid fa-paper-plane"></i>
          <h3>Compose Notification</h3>
        </div>

        <form method="post" action="<?= e($B) ?>/admin/admin-notifications/send" class="compose-form">
          <div class="form-grid">
            <div class="field-group">
              <label for="template_key">Template (Optional)</label>
              <select name="template_key" id="template_key">
                <option value="">- No template (custom) -</option>
                <?php foreach (($templates ?? []) as $t): ?>
                  <option value="<?= e($t['template_key'] ?? '') ?>">
                    <?= e($t['title'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <p class="hint">Use templates for promotion, holiday hours, branch closure, or new service announcements.
              </p>
            </div>

            <div class="field-group full">
              <label for="details">Details (used by template as {{details}})</label>
              <textarea name="details" id="details" placeholder="Example: 20% off brake pads until Feb 28"></textarea>
            </div>

            <div class="field-group">
              <label for="audience">Recipients</label>
              <select name="audience" id="audience">
                <option value="all_customers">All Customers</option>
                <option value="upcoming_appointments">Customers with Upcoming Appointments</option>
                <option value="recent_customers">Recent Customers</option>
                <option value="custom">Custom Emails</option>
              </select>

              <div id="upcomingBox" class="conditional-box" hidden>
                <label for="upcoming_days">Upcoming window (days)</label>
                <input type="number" id="upcoming_days" name="upcoming_days" value="1" min="1" max="30">
              </div>

              <div id="recentBox" class="conditional-box" hidden>
                <label for="recent_days">Recent window (days)</label>
                <input type="number" id="recent_days" name="recent_days" value="30" min="1" max="365">
              </div>

              <div id="customBox" class="conditional-box" hidden>
                <label for="custom_emails">Custom emails (comma/newline separated)</label>
                <textarea id="custom_emails" name="custom_emails" placeholder="a@gmail.com, b@gmail.com"></textarea>
              </div>

              <div id="selectBox" class="conditional-box" hidden>
                <label for="userSearch">Search customers</label>
                <input type="text" id="userSearch" placeholder="Type name or email...">
                <p class="hint">Click Add to include recipients.</p>

                <div id="pickerResults" class="picker-results"></div>

                <div id="pickedUsers" class="picked-users"></div>
                <div id="pickedInputs"></div>
              </div>
            </div>

            <div class="field-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject"
                placeholder="Enter subject (or leave blank to use template)">
            </div>

            <div class="field-group full message-box">
              <label for="message">Message</label>
              <textarea id="message" name="message"
                placeholder="Write your message (or leave blank to use template)"></textarea>
              <p class="hint">Tip: Keep it short and clear. Include date/time, branch name, and contact number.</p>

              <div class="send-actions">
                <button type="submit" class="send-btn">
                  <i class="fas fa-paper-plane"></i>
                  <span>Send Notification</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </section>

      <section class="table-wrap">
        <div class="section-head">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <h3>Recent Notifications</h3>
        </div>

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
              <tr>
                <td colspan="7" class="empty-row">
                  <i class="fa-regular fa-bell"></i>
                  No notifications yet.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($recent as $n): ?>
                <?php
                $st = strtolower((string) ($n['status'] ?? 'queued'));
                $pillClass = $st === 'sent' ? 'status--active' : ($st === 'failed' ? 'status--inactive' : 'status--pending');
                ?>
                <tr>
                  <td><?= e($n['subject'] ?? '-') ?></td>
                  <td><?= e(ucwords(str_replace('_', ' ', (string) ($n['audience'] ?? '-')))) ?></td>
                  <td>
                    <span class="status-pill <?= e($pillClass) ?>">
                      <span class="dot"></span>
                      <?= e(ucfirst($st)) ?>
                    </span>
                  </td>
                  <td><?= (int) ($n['recipients_total'] ?? 0) ?></td>
                  <td><?= (int) ($n['recipients_sent'] ?? 0) ?></td>
                  <td><?= (int) ($n['recipients_failed'] ?? 0) ?></td>
                  <td><?= e($n['created_at'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>

  <script>
    const BASE = "<?= e($B) ?>";

    const audience = document.getElementById('audience');
    const upcomingBox = document.getElementById('upcomingBox');
    const recentBox = document.getElementById('recentBox');
    const customBox = document.getElementById('customBox');
    const selectBox = document.getElementById('selectBox');

    function toggleBoxes() {
      const v = audience.value;
      upcomingBox.hidden = (v !== 'upcoming_appointments');
      recentBox.hidden = (v !== 'recent_customers');
      customBox.hidden = (v !== 'custom');
      selectBox.hidden = (v !== 'selected_users');
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
        pill.innerHTML = `${u.name} (${u.email}) <button type="button" data-id="${id}" aria-label="Remove recipient">x</button>`;
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
            <button type="button" class="picker-add-btn">Add</button>
          </div>
        `;

        div.querySelector('button').addEventListener('click', () => {
          picked.set(String(r.user_id), {
            name,
            email: r.email
          });
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
          if (!q) {
            pickerResults.innerHTML = '';
            return;
          }
          const rows = await searchUsers(q);
          renderResults(rows);
        }, 250);
      });
    }
  </script>

</body>

</html>