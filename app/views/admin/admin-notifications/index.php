<?php $current = 'notifications'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AutoNexus - Notifications</title>

  <!-- Shared neutral styles -->
  <link rel="stylesheet" href="../admin-shared/management.css">
  <!-- Sidebar styles -->
  <link rel="stylesheet" href="../admin-sidebar/styles.css">
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Page-specific styles -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include(__DIR__ . '/../admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <!-- Header -->
 

  <!-- Notifications Section -->
  <section>
    <h2>Notifications</h2>

    <!-- Compose Notification -->
    <div class="card">
      <h3>Compose Notification</h3>
      <form>
        <div class="compose-group">
          <label>Recipients</label>
          <div class="radio-group">
            <label><input type="radio" name="recipients" checked> All Customers</label>
            <label><input type="radio" name="recipients"> Customers with Upcoming Appointments</label>
            <label><input type="radio" name="recipients"> Recent Customers (Last 30 Days)</label>
            <label><input type="radio" name="recipients"> Custom</label>
          </div>
        </div>

        <div class="compose-group">
          <label>Notification Type</label>
          <div class="radio-group">
            <label><input type="radio" name="type" checked> Email</label>
            <label><input type="radio" name="type"> SMS</label>
            <label><input type="radio" name="type"> Both</label>
          </div>
        </div>

        <div class="compose-group">
          <label>Subject</label>
          <input type="text" placeholder="Enter subject">
        </div>

        <div class="compose-group">
          <label>Message</label>
          <textarea placeholder="Write your message"></textarea>
        </div>

        <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i> Send Notification</button>
      </form>
    </div>

    <!-- Recent Notifications -->
    <div class="card">
      <h3>Recent Notifications</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Recipients</th>
            <th>Type</th>
            <th>Sent At</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Appointment Reminder</td>
            <td>All Customers with Appointments Tomorrow</td>
            <td>Email</td>
            <td>Nov 9, 2025</td>
          </tr>
          <tr>
            <td>Service Completed</td>
            <td>John Smith</td>
            <td>SMS</td>
            <td>Nov 9, 2025</td>
          </tr>
          <tr>
            <td>Special Discount Offer</td>
            <td>All Customers</td>
            <td>Email &amp; SMS</td>
            <td>Nov 8, 2025</td>
          </tr>
          <tr>
            <td>Service Follow-up</td>
            <td>Customers with Service in Last Week</td>
            <td>Email</td>
            <td>Nov 7, 2025</td>
          </tr>
          <tr>
            <td>Holiday Hours Update</td>
            <td>All Customers</td>
            <td>Email</td>
            <td>Nov 6, 2025</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

</body>
</html>
