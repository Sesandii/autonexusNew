<?php
require_once 'db.php';

// Helper: redirect back
function back() {
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
function clean($v) {
    return trim($v);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_job') {
        $vehicle_id = intval($_POST['vehicle_id'] ?? 0);
        $mechanic_id = intval($_POST['assigned_mechanic_id'] ?? 0);
        $job_title = clean($_POST['job_title'] ?? '');
        $notes = clean($_POST['notes'] ?? '');
        $created_by = clean($_POST['created_by'] ?? 'Supervisor');

        if ($vehicle_id && $mechanic_id && $job_title !== '') {
            $stmt = $pdo->prepare("INSERT INTO jobs (vehicle_id, assigned_mechanic_id, job_title, notes, created_by) VALUES (?,?,?,?,?)");
            $stmt->execute([$vehicle_id, $mechanic_id, $job_title, $notes, $created_by]);
            $pdo->prepare("UPDATE vehicles SET status='in_service' WHERE vehicle_id=?")->execute([$vehicle_id]);
        }
        back();
    }

    if ($action === 'update_job') {
      $job_id = intval($_POST['job_id'] ?? 0);
      $vehicle_id = intval($_POST['vehicle_id'] ?? 0);
      $mechanic_id = intval($_POST['assigned_mechanic_id'] ?? 0);
      $job_title = clean($_POST['job_title'] ?? '');
      $notes = clean($_POST['notes'] ?? '');
      $status = $_POST['status'] ?? 'assigned';
  
      if ($job_id && $mechanic_id && $job_title !== '') {
          $stmt = $pdo->prepare("UPDATE jobs 
                                 SET assigned_mechanic_id=?, job_title=?, notes=?, status=?, updated_at=NOW() 
                                 WHERE job_id=?");
          $stmt->execute([$mechanic_id, $job_title, $notes, $status, $job_id]);
      }
  
      back();
  }
  
  
  

    if ($action === 'delete_job') {
        $job_id = intval($_POST['job_id'] ?? 0);
        if ($job_id) {
            $stmt = $pdo->prepare("SELECT vehicle_id FROM jobs WHERE job_id=?");
            $stmt->execute([$job_id]);
            $row = $stmt->fetch();
            if ($row) {
                $vehicle_id = $row['vehicle_id'];
                $pdo->prepare("DELETE FROM jobs WHERE job_id=?")->execute([$job_id]);
                $pdo->prepare("UPDATE vehicles SET status='available' WHERE vehicle_id=?")->execute([$vehicle_id]);
            }
        }
        back();
    }
}

// Fetch lists
$availableVehicles = $pdo->query("SELECT vehicle_id, vehicle_code, model, status FROM vehicles WHERE status = 'available' ORDER BY vehicle_code")->fetchAll();
$activeMechanics = $pdo->query("SELECT mechanic_id, name, is_active FROM mechanics WHERE is_active = 1 ORDER BY name")->fetchAll();
$jobs = $pdo->query("SELECT j.*, v.vehicle_code, v.model, m.name AS mechanic_name FROM jobs j JOIN vehicles v ON j.vehicle_id = v.vehicle_id JOIN mechanics m ON j.assigned_mechanic_id = m.mechanic_id ORDER BY j.created_at DESC")->fetchAll();
$allVehicles = $pdo->query("SELECT vehicle_id, vehicle_code, model, status FROM vehicles ORDER BY vehicle_code")->fetchAll();
$allMechanics = $pdo->query("SELECT mechanic_id, name, is_active FROM mechanics ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoNexus Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-jobs.css"/>
</head>
<body>
  <div class="sidebar">
     <div class="logo-container">
       <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
     <h2>AUTONEXUS</h2>
     <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
     <a href="/autonexus/supervisor/jobs" class="nav active"><img src="/autonexus/public/assets/img/jobs.png"/>Jobs</a>
     <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs</a>
     <a href="/autonexus/supervisor/history"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
     <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
     <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
     <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
  </div>

  <main class="main-content">
    <header>
      <input type="text" placeholder="Search..." class="search" />
      <div class="user-profile">
        <img src="/autonexus/public/assets/img/bell.png" alt="Notifications" class="icon" />
        <img src="/autonexus/public/assets/img/user.png" alt="User" class="avatar-img" />
        <span>John Doe</span>
      </div>
    </header>

    <section class="job-section">
      <div class="job-header">
        <h2>Ongoing Jobs</h2>
        <button id="addJobBtn" class="btn btn-red">Add Job</button>
      </div>

      <table>
        <thead>
          <tr>
            <th>Job ID</th>
            <th>Vehicle</th>
            <th>Mechanic</th>
            <th>Title</th>
            <th>Notes</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
<?php if (empty($jobs)): ?>
  <tr><td colspan="8">No jobs yet</td></tr>
<?php else: ?>
  <?php foreach ($data['jobs'] as $j): ?>
    <tr data-job='<?php echo json_encode($j, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
      <td><?php echo $j['job_id']; ?></td>
      <td><?php echo htmlspecialchars($j['vehicle_code']); ?><br><small><?php echo htmlspecialchars($j['model']); ?></small></td>
      <td><?php echo htmlspecialchars($j['mechanic_name']); ?></td>
      <td><?php echo htmlspecialchars($j['job_title']); ?></td>
      <td><?php echo htmlspecialchars($j['notes']); ?></td>
      <td><?php echo htmlspecialchars($j['status']); ?></td>
      <td><?php echo htmlspecialchars($j['created_at']); ?></td>
      <td>
        <button type="button" class="btn btn-sm view-btn">View</button>
        <button type="button" class="btn btn-sm edit-btn">Edit</button>
        <form class="inline-form" method="post" onsubmit="return confirm('Delete this job?');">
          <input type="hidden" name="action" value="delete_job">
          <input type="hidden" name="job_id" value="<?php echo $j['job_id']; ?>">
          <button type="submit" class="btn btn-sm">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>


      </table>
    </section>
  </main>

  <!-- Job Modal -->
  <div id="jobModal" class="modal" aria-hidden="true">
    <div class="modal-dialog">
      <button class="modal-close" id="modalClose">&times;</button>
      <h3 id="modalTitle">Assign New Job</h3>

      <form id="jobForm" method="post">
        <input type="hidden" id="job_id" name="job_id" value="">
        <input type="hidden" id="form_action" name="action" value="create_job">

        <label>Job ID (auto-generated)</label>
        <input type="text" id="jobIdDisplay" disabled placeholder="Auto-generated on creation">

        <label for="vehicle">Vehicle</label>
        <select id="vehicle" name="vehicle_id" required>
          <option value="">Select a vehicle</option>
          <?php foreach ($data['allVehicles'] as $v): ?>
  <option value="<?php echo $v['vehicle_id']; ?>" <?php echo $v['status'] !== 'available' ? 'disabled' : ''; ?>>
    <?php echo htmlspecialchars($v['vehicle_code'].' — '.$v['model'].' ('.$v['status'].')'); ?>
  </option>
<?php endforeach; ?>

        </select>

        <label for="msechanic">Mechanic</label>
        <select id="mechanic" name="assigned_mechanic_id" required>
          <option value="">Select a mechanic</option>
          <?php foreach ($activeMechanics as $m): ?>
            <option value="<?php echo $m['mechanic_id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
          <?php endforeach; ?>
        </select>

        <label for="job_title">Job Title</label>
        <input id="job_title" name="job_title" required>

        <label for="notes">Notes</label>
        <textarea id="notes" name="notes"></textarea>

        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="assigned">assigned</option>
          <option value="in_progress">in_progress</option>
          <option value="completed">completed</option>
          <option value="cancelled">cancelled</option>
        </select>

        <input type="hidden" name="created_by" value="Supervisor">

        <div class="modal-actions">
          <button type="button" id="cancelBtn" class="btn">Cancel</button>
          <button type="submit" id="saveBtn" class="btn btn-red">Create Job</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  const ALL_VEHICLES = <?php echo json_encode($allVehicles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const ALL_MECHANICS = <?php echo json_encode($allMechanics, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
  <script src="/autonexus/public/assets/js/supervisor/script-jobs.js"></script>
</body>
</html>