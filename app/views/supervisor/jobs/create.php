<?php
// app/views/supervisor/jobs/create.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Job</title>
</head>
<body>

  <div class="container" style="max-width: 700px; margin: 50px auto;">
    <h2>Create New Job</h2>

    <form action="/supervisor/jobs/store" method="POST" style="margin-top: 20px;">
      
      <!-- Job Title -->
      <div class="form-group" style="margin-bottom: 15px;">
        <label for="job_title">Job Title:</label><br>
        <input type="text" name="job_title" id="job_title" required 
               style="width:100%; padding:8px;">
      </div>

      <!-- Vehicle Dropdown -->
      <div class="form-group" style="margin-bottom: 15px;">
        <label for="vehicle_id">Select Vehicle:</label><br>
        <select name="vehicle_id" id="vehicle_id" required style="width:100%; padding:8px;">
          <option value="">-- Select Available Vehicle --</option>
          <?php if (!empty($vehicles)): ?>
            <?php foreach ($vehicles as $v): ?>
              <option value="<?= htmlspecialchars($v['vehicle_id']) ?>">
                <?= htmlspecialchars($v['vehicle_code']) ?> - <?= htmlspecialchars($v['model']) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">No vehicles available</option>
          <?php endif; ?>
        </select>
      </div>

      <!-- Mechanic Dropdown -->
      <div class="form-group" style="margin-bottom: 15px;">
        <label for="assigned_mechanic_id">Assign Mechanic:</label><br>
        <select name="assigned_mechanic_id" id="assigned_mechanic_id" required style="width:100%; padding:8px;">
          <option value="">-- Select Available Mechanic --</option>
          <?php if (!empty($mechanics)): ?>
            <?php foreach ($mechanics as $m): ?>
              <option value="<?= htmlspecialchars($m['mechanic_id']) ?>">
                <?= htmlspecialchars($m['name']) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">No mechanics available</option>
          <?php endif; ?>
        </select>
      </div>

      <!-- Notes -->
      <div class="form-group" style="margin-bottom: 15px;">
        <label for="notes">Notes:</label><br>
        <textarea name="notes" id="notes" rows="4" style="width:100%; padding:8px;"></textarea>
      </div>

      <!-- Submit -->
      <div class="form-group">
        <button type="submit" style="padding:10px 20px;">Create Job</button>
        <a href="/supervisor/jobs" style="margin-left:10px;">Cancel</a>
      </div>

    </form>
  </div>

</body>
</html>
