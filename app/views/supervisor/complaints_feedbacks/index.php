<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Complaints & Feedbacks</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-complaints.css"/>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-feedbacks.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<main class="main-content">
  <!-- Toggle Buttons -->
  <header class="page-header">
  <h1>Customer Complaints & Feedbacks</h1>
    <div class="table-toggle">
      <button class="toggle-btn active" data-target="complaints">Complaints</button>
      <button class="toggle-btn" data-target="feedbacks">Feedbacks</button>
    </div>
    
  </header>

  <!-- Complaints Filters -->
  <div id="complaints-filters" class="filter-bar">
    <input type="text" placeholder="Search complaints..." class="search" id="searchComplaints"/>
    <input type="date" id="dateComplaints"/>
    <select id="statusComplaints">
      <option value="">All Status</option>
      <option value="open">Open</option>
      <option value="in_progress">In Progress</option>
      <option value="resolved">Resolved</option>
    </select>
    <select id="priorityComplaints">
      <option value="">All Priority</option>
      <option value="low">Low</option>
      <option value="medium">Medium</option>
      <option value="high">High</option>
    </select>
    <button type="button" id="resetComplaints" class="reset-btn">Reset</button>
  </div>

  <!-- Complaints Section -->
  <section class="data-section" id="complaints">
    <div class="complaints-grid">
      <?php if (!empty($complaints)): ?>
        <?php foreach ($complaints as $c): ?>
          <div class="complaint-row"
               data-date="<?= htmlspecialchars($c['complaint_date']); ?>"
               data-status="<?= strtolower($c['status']); ?>"
               data-priority="<?= strtolower($c['priority']); ?>">

            <div class="complaint-header">
              <div>
                <h3><?= htmlspecialchars($c['customer_name']); ?></h3>
                <p class="meta">
                  <strong>Vehicle:</strong> <?= htmlspecialchars($c['vehicle']); ?> (<?= htmlspecialchars($c['vehicle_number']); ?>)
                  &nbsp; | &nbsp;
                  <strong>Date:</strong> <?= htmlspecialchars($c['complaint_date']); ?> <?= htmlspecialchars($c['complaint_time']); ?>
                </p>
              </div>
              <span class="priority <?= strtolower($c['priority']); ?>">
                <?= htmlspecialchars($c['priority']); ?>
              </span>
            </div>

            <p class="description"><?= htmlspecialchars($c['description']); ?></p>

            <div class="complaint-footer">
              <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($c['status']); ?></span></p>
              <p><strong>Assigned To:</strong> <?= htmlspecialchars($c['assigned_to']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-data">No complaints found.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Feedbacks Section -->
  <section class="data-section hidden" id="feedbacks">
    <div id="feedback-filters" class="filter-bar">
      <input type="text" placeholder="Search by customer, service, comment..." class="search" id="searchFeedback"/>
      <select id="ratingFilter">
        <option value="">All Ratings</option>
        <option value="5">5 ★</option>
        <option value="4">4 ★</option>
        <option value="3">3 ★</option>
        <option value="2">2 ★</option>
        <option value="1">1 ★</option>
      </select>
      <input type="date" id="dateFeedback"/>
      <button type="button" id="resetFeedback" class="reset-btn">Reset</button>
    </div>

    <div class="feedback-cards">
      <?php if (!empty($feedbacks)): ?>
        <?php foreach ($feedbacks as $f): ?>
          <div class="card"
               data-rating="<?= (int)$f['rating'] ?>"
               data-date="<?= date('Y-m-d', strtotime($f['created_at'])) ?>">

            <h3><?= htmlspecialchars($f['customer_name']) ?>
              <span class="rating <?= ($f['rating'] >= 4 ? 'good' : ($f['rating'] >= 2 ? 'avg' : 'bad')) ?>">
                <?= htmlspecialchars($f['rating']) ?>/5 ★
              </span>
            </h3>
            <p><strong>Service:</strong> <?= htmlspecialchars($f['service_name']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($f['created_at']) ?></p>
            <p><?= htmlspecialchars($f['comment']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No feedbacks found.</p>
      <?php endif; ?>
    </div>
  </section>

</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // ---- Toggle Sections ----
  const buttons = document.querySelectorAll(".toggle-btn");
  const sections = document.querySelectorAll(".data-section");
  const complaintsFilters = document.getElementById("complaints-filters");
  const feedbackFilters = document.getElementById("feedback-filters");

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      buttons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      sections.forEach(sec => sec.classList.add("hidden"));
      const target = document.getElementById(btn.dataset.target);
      if(target) target.classList.remove("hidden");

      // Show relevant filters
      complaintsFilters.style.display = (btn.dataset.target === "complaints") ? "flex" : "none";
      feedbackFilters.style.display = (btn.dataset.target === "feedbacks") ? "flex" : "none";
    });
  });

  // ---- Complaints Filters ----
  const searchComplaints = document.getElementById("searchComplaints");
  const dateComplaints = document.getElementById("dateComplaints");
  const statusComplaints = document.getElementById("statusComplaints");
  const priorityComplaints = document.getElementById("priorityComplaints");
  const complaintRows = document.querySelectorAll("#complaints .complaint-row");
  const resetComplaints = document.getElementById("resetComplaints");

  function filterComplaints() {
    const searchVal = searchComplaints.value.toLowerCase();
    const dateVal = dateComplaints.value;
    const statusVal = statusComplaints.value;
    const priorityVal = priorityComplaints.value;

    complaintRows.forEach(row => {
      const rowText = row.innerText.toLowerCase();
      const rowDate = row.dataset.date;
      const rowStatus = row.dataset.status;
      const rowPriority = row.dataset.priority;

      const match = (!searchVal || rowText.includes(searchVal))
                    && (!dateVal || rowDate === dateVal)
                    && (!statusVal || rowStatus === statusVal)
                    && (!priorityVal || rowPriority === priorityVal);
      row.style.display = match ? "block" : "none";
    });
  }

  searchComplaints.addEventListener("keyup", filterComplaints);
  dateComplaints.addEventListener("change", filterComplaints);
  statusComplaints.addEventListener("change", filterComplaints);
  priorityComplaints.addEventListener("change", filterComplaints);

  resetComplaints.addEventListener("click", () => {
  searchComplaints.value = "";
  dateComplaints.value = "";
  statusComplaints.value = "";
  priorityComplaints.value = "";

  complaintRows.forEach(row => {
    row.style.display = "block";
  });
});


// ---- Feedback Filters ----
const searchFeedback = document.getElementById("searchFeedback");
const dateFeedback = document.getElementById("dateFeedback");
const ratingFilter = document.getElementById("ratingFilter");
const feedbackCards = document.querySelectorAll("#feedbacks .card");
const resetFeedback = document.getElementById("resetFeedback");

function filterFeedback() {
  const searchVal = searchFeedback.value.toLowerCase();
  const dateVal = dateFeedback.value;
  const ratingVal = ratingFilter.value;

  feedbackCards.forEach(card => {
    const text = card.innerText.toLowerCase();
    const cardDate = card.dataset.date;
    const rating = card.dataset.rating;

    const match = (!searchVal || text.includes(searchVal))
                  && (!dateVal || cardDate === dateVal)
                  && (!ratingVal || rating === ratingVal);

    card.style.display = match ? "block" : "none";
  });
}

searchFeedback.addEventListener("keyup", filterFeedback);
dateFeedback.addEventListener("change", filterFeedback);
ratingFilter.addEventListener("change", filterFeedback);

resetFeedback.addEventListener("click", () => {
  searchFeedback.value = "";
  dateFeedback.value = "";
  ratingFilter.value = "";

  feedbackCards.forEach(card => {
    card.style.display = "block";
  });
});
});
</script>

</body>
</html>
