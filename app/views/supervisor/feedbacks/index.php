<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Feedback</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-feedbacks.css"/>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">
<header class="page-header">
  <h1>Customer Feedback</h1>

  <div class="filter-bar">
    <input
      type="text"
      placeholder="Search by customer, service, comment..."
      class="search"
      id="searchInput"
    />

    <select id="ratingFilter">
      <option value="">All Ratings</option>
      <option value="5">5 ★</option>
      <option value="4">4 ★</option>
      <option value="3">3 ★</option>
      <option value="2">2 ★</option>
      <option value="1">1 ★</option>
    </select>

    <select id="replyFilter">
      <option value="">All Replies</option>
      <option value="replied">Replied</option>
      <option value="not-replied">Not Replied</option>
    </select>

    <input type="date" id="dateFilter">
  </div>
</header>


  <section class="feedback-section">

    <div class="feedback-cards">
      <?php if (!empty($feedbacks)): ?>
        <?php foreach ($feedbacks as $f): ?>
          <div class="card"
     data-rating="<?= (int)$f['rating'] ?>"
     data-replied="<?= $f['replied_status'] ? 'replied' : 'not-replied' ?>"
     data-date="<?= date('Y-m-d', strtotime($f['created_at'])) ?>">

            <h3><?= htmlspecialchars($f['customer_name']) ?>
              <span class="rating <?= ($f['rating'] >= 4 ? 'good' : ($f['rating'] >= 2 ? 'avg' : 'bad')) ?>">
                <?= htmlspecialchars($f['rating']) ?>/5 ★
              </span>
            </h3>
            <p><strong>Service:</strong> <?= htmlspecialchars($f['service_name']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($f['created_at']) ?></p>
            <p><?= htmlspecialchars($f['comment']) ?></p>
            <span class="reply <?= ($f['replied_status'] ? 'replied' : 'not-replied') ?>">
              <?= ($f['replied_status'] ? 'Replied' : 'Not replied yet') ?>
            </span>
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
    const searchInput = document.getElementById("searchInput");
    const ratingFilter = document.getElementById("ratingFilter");
    const replyFilter = document.getElementById("replyFilter");
    const dateFilter = document.getElementById("dateFilter");

    const cards = document.querySelectorAll(".feedback-cards .card");

    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase();
        const ratingVal = ratingFilter.value;
        const replyVal = replyFilter.value;
        const dateVal = dateFilter.value; // YYYY-MM-DD

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            const rating = card.dataset.rating;   // "1" | "2" | "3" | "4" | "5"
            const replied = card.dataset.replied; // "yes" | "no"
            const cardDate = card.dataset.date;   // YYYY-MM-DD

            const matchSearch = !searchVal || text.includes(searchVal);
            const matchRating = !ratingVal || rating === ratingVal;
            const matchReply = !replyVal || replied === replyVal;
            const matchDate = !dateVal || cardDate === dateVal;

            card.style.display =
                matchSearch && matchRating && matchReply && matchDate
                    ? "block"
                    : "none";
        });
    }

    searchInput.addEventListener("keyup", applyFilters);
    ratingFilter.addEventListener("change", applyFilters);
    replyFilter.addEventListener("change", applyFilters);
    dateFilter.addEventListener("change", applyFilters);
});
</script>


</body>
</html>
