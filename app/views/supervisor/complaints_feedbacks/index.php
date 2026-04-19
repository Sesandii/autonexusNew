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
<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['message'])): 
    $message = $_SESSION['message'];
?>
    <div class="toast-container" id="toast-notification">
        <div class="toast-message <?= $message['type'] ?>">
            <span><?= htmlspecialchars($message['text']) ?></span>
        </div>
    </div>
<?php 
    unset($_SESSION['message']); // Crucial: clear it so it doesn't repeat
endif; 
?>
<main class="main-content">
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Complaints & Feedbacks <span class="sep"></span> 
  </div>
  <header class="page-header">
  <h1>Customer Complaints & Feedbacks</h1>
    <div class="table-toggle">
      <button class="toggle-btn active" data-target="complaints">Complaints</button>
      <button class="toggle-btn" data-target="feedbacks">Feedbacks</button>
    </div>
    
  </header>

  <div id="complaints-filters" class="filter-bar">
    <input type="text" placeholder="Search complaints..." class="search" id="searchComplaints"/>
    <input type="date" id="dateComplaints"/>
    <select id="statusComplaints">
      <option value="">All Status</option>
      <option value="open">Open</option>
      <option value="in_progress">In Progress</option>
      <option value="resolved">Resolved</option>
      <option value="closed">Closed</option>
    </select>
    <select id="priorityComplaints">
      <option value="">All Priority</option>
      <option value="low">Low</option>
      <option value="medium">Medium</option>
      <option value="high">High</option>
    </select>
    <button type="button" id="resetComplaints" class="reset-btn">Reset</button>
  </div>

<section class="data-section" id="complaints">
  <div class="complaints-grid">
    <?php if (!empty($complaints)): ?>
      <?php foreach ($complaints as $c): ?>
        <div class="complaint-row"
             data-date="<?= date('Y-m-d', strtotime($c['created_at'])); ?>"
             data-status="<?= strtolower($c['status']); ?>"
             data-priority="<?= strtolower($c['priority']); ?>">

          <div class="complaint-header">
            <div>
              <h3><?= htmlspecialchars($c['customer_name']); ?></h3>
              <p class="meta">
                <strong>Vehicle:</strong> <?= htmlspecialchars($c['vehicle']); ?>
                (<?= htmlspecialchars($c['vehicle_number']); ?>)
                &nbsp; | &nbsp;
                <strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($c['created_at'])); ?>
              </p>
            </div>
            <span class="priority <?= strtolower($c['priority']); ?>">
              <?= htmlspecialchars($c['priority']); ?>
            </span>
          </div>

          <p class="description"><?= htmlspecialchars($c['description']); ?></p>

          <div class="complaint-footer">
    <form action="<?= $base ?>/supervisor/complaints_feedbacks/updateComplaintStatus" method="POST">
        <input type="hidden" name="complaint_id" value="<?= $c['complaint_id']; ?>">
        
        <p><strong>Status:</strong> 
            <select name="status" onchange="this.form.submit()" class="status-dropdown">
                <option value="open" <?= $c['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                <option value="in_progress" <?= $c['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="resolved" <?= $c['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="closed" <?= $c['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </p>
    </form>
    <p><strong>Assigned To:</strong> <?= htmlspecialchars($c['assigned_user_name'] ?? 'Unassigned'); ?></p>
</div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-data">No complaints found.</p>
    <?php endif; ?>
  </div>
</section>

<section class="data-section hidden" id="feedbacks">
  <div id="feedback-filters" class="filter-bar">
    <input type="text" placeholder="Search by customer, service..." class="search" id="searchFeedback"/>
    <select id="ratingFilter">
      <option value="">All Ratings</option>
      <option value="5">5/5 </option>
      <option value="4">4/5 </option>
      <option value="3">3/5 </option>
      <option value="2">2/5 </option>
      <option value="1">1/5 </option>
    </select>
    <input type="date" id="dateFeedback"/>
    <button type="button" id="resetFeedback" class="reset-btn">Reset</button>
  </div>

  <div class="feedback-grid">
    <?php if (!empty($feedbacks)): ?>
        <?php foreach ($feedbacks as $f): ?>
            <div class="feedback-card" 
                 data-rating="<?= (int)$f['rating'] ?>" 
                 data-date="<?= date('Y-m-d', strtotime($f['created_at'])) ?>">
                
                <div class="card-header">
                    <div class="user-info">
                        <h3 class="customer-name"><?= htmlspecialchars($f['customer_name']); ?></h3>
                        <p class="detail-item"><strong>Service:</strong> <?= htmlspecialchars($f['service_name'] ?? 'N/A'); ?></p>
                        <p class="detail-item"><strong>Vehicle:</strong> <?= htmlspecialchars($f['vehicle'] ?? '-'); ?></p>
                        <p class="detail-item"><strong>Date:</strong> <?= date('M j, Y', strtotime($f['created_at'])); ?></p>
                    </div>
                    <div class="rating-container">
    <?php 
        $ratingClass = 'rate-red'; 
        if ($f['rating'] >= 4) {
            $ratingClass = 'rate-green';
        } elseif ($f['rating'] >= 2) {
            $ratingClass = 'rate-yellow';
        }
    ?>
    <span class="rating-badge <?= $ratingClass ?>">
        <?= htmlspecialchars($f['rating']); ?>/5
    </span>
</div>
                </div>

                <p class="comment-text">"<?= htmlspecialchars($f['comment']); ?>"</p>

                <div class="reply-section">
    <?php if (!empty($f['reply_text'])): ?>
        <div id="display-reply-<?= $f['feedback_id']; ?>">
            <span class="reply-status">Replied</span>
            <button type="button" class="edit-reply-btn" onclick="toggleEdit(<?= $f['feedback_id']; ?>)">Edit</button>
            <label class="reply-label">Your Reply:</label>
            <p class="current-reply"><?= htmlspecialchars($f['reply_text']); ?></p>
        </div>

        <div id="edit-form-<?= $f['feedback_id']; ?>" style="display: none;">
    <span class="reply-status">Editing Reply</span>
    <form action="<?= $base ?>/supervisor/complaints_feedbacks/addFeedbackReply" method="POST" class="reply-form">
        <input type="hidden" name="feedback_id" value="<?= $f['feedback_id']; ?>">
        <textarea name="reply_text" required><?= htmlspecialchars($f['reply_text']); ?></textarea>
        
        <div class="reply-actions">
            <button type="submit" class="submit-reply-btn">Update</button>
            <button type="button" class="cancel-btn" onclick="toggleEdit(<?= $f['feedback_id']; ?>)">Cancel</button>
        </div>
    </form>
</div>

    <?php else: ?>
        <span class="reply-status pending">Pending Reply</span>
        <form action="<?= $base ?>/supervisor/complaints_feedbacks/addFeedbackReply" method="POST" class="reply-form">
            <input type="hidden" name="feedback_id" value="<?= $f['feedback_id']; ?>">
            <textarea name="reply_text" placeholder="Write your reply here..." required></textarea>
            <button type="submit" class="submit-reply-btn">Send Reply</button>
        </form>
    <?php endif; ?>
</div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-data">No feedbacks found.</p>
    <?php endif; ?>
</div>
</section>

</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".toggle-btn");
  const sections = document.querySelectorAll(".data-section");
  const complaintsFilters = document.getElementById("complaints-filters");
  const feedbackFilters = document.getElementById("feedback-filters");
  
  function switchTab(targetId) {
    const btn = document.querySelector(`.toggle-btn[data-target="${targetId}"]`);
    if (btn) {
      buttons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      sections.forEach(sec => sec.classList.add("hidden"));
      const targetSection = document.getElementById(targetId);
      if (targetSection) targetSection.classList.remove("hidden");

      if (complaintsFilters) complaintsFilters.style.display = (targetId === "complaints") ? "flex" : "none";
      if (feedbackFilters) feedbackFilters.style.display = (targetId === "feedbacks") ? "flex" : "none";
    }
  }

  <?php if (isset($_SESSION['active_tab'])): ?>
    const sessionTab = "<?= $_SESSION['active_tab']; ?>";
    switchTab(sessionTab);
    <?php unset($_SESSION['active_tab']);?>
  <?php else: ?>
    if (window.location.hash) {
      switchTab(window.location.hash.substring(1));
    }
  <?php endif; ?>

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      switchTab(btn.dataset.target);
    });
  });

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

      const match = (!searchVal || rowText.includes(searchVal)) &&
        (!dateVal || rowDate === dateVal) &&
        (!statusVal || rowStatus === statusVal) &&
        (!priorityVal || rowPriority === priorityVal);
      row.style.display = match ? "block" : "none";
    });
  }

  if (searchComplaints) {
    searchComplaints.addEventListener("keyup", filterComplaints);
    dateComplaints.addEventListener("change", filterComplaints);
    statusComplaints.addEventListener("change", filterComplaints);
    priorityComplaints.addEventListener("change", filterComplaints);
  }

  if (resetComplaints) {
    resetComplaints.addEventListener("click", () => {
      searchComplaints.value = "";
      dateComplaints.value = "";
      statusComplaints.value = "";
      priorityComplaints.value = "";
      complaintRows.forEach(row => row.style.display = "block");
    });
  }

  const searchFeedback = document.getElementById("searchFeedback");
  const dateFeedback = document.getElementById("dateFeedback");
  const ratingFilter = document.getElementById("ratingFilter");
  const feedbackCards = document.querySelectorAll(".feedback-card");
  const resetFeedback = document.getElementById("resetFeedback");

  function filterFeedback() {
    const searchVal = searchFeedback.value.toLowerCase();
    const dateVal = dateFeedback.value;
    const ratingVal = ratingFilter.value;

    feedbackCards.forEach(card => {
      const text = card.textContent.toLowerCase();
      const cardDate = card.dataset.date;
      const cardRating = card.dataset.rating;

      const matchesSearch = !searchVal || text.includes(searchVal);
      const matchesDate = !dateVal || cardDate === dateVal;
      const matchesRating = !ratingVal || cardRating === ratingVal;

      card.style.display = (matchesSearch && matchesDate && matchesRating) ? "block" : "none";
    });
  }

  if (searchFeedback) {
    searchFeedback.addEventListener("input", filterFeedback);
    dateFeedback.addEventListener("change", filterFeedback);
    ratingFilter.addEventListener("change", filterFeedback);
  }

  if (resetFeedback) {
    resetFeedback.addEventListener("click", () => {
      searchFeedback.value = "";
      dateFeedback.value = "";
      ratingFilter.value = "";
      feedbackCards.forEach(card => card.style.display = "block");
    });
  }
});

window.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});

function toggleEdit(id) {
  const displayDiv = document.getElementById(`display-reply-${id}`);
  const editDiv = document.getElementById(`edit-form-${id}`);

  if (displayDiv && editDiv) {
    if (displayDiv.style.display === "none") {
      displayDiv.style.display = "block";
      editDiv.style.display = "none";
    } else {
      displayDiv.style.display = "none";
      editDiv.style.display = "block";
    }
  }
}
</script>

</body>
</html>
