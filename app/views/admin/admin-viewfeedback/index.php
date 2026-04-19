<?php /* Admin view: renders admin-viewfeedback/index page. */ ?>
<?php
/** @var array       $feedbacks */
/** @var array       $filters */
/** @var float|null  $avgRating */
/** @var string      $current */

$current = $current ?? 'feedback';
$B = rtrim(BASE_URL, '/');

$q = htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8');
$ratingF = $filters['rating'] ?? '';
$repliedF = $filters['replied'] ?? '';
$dateF = htmlspecialchars($filters['date'] ?? '', ENT_QUOTES, 'UTF-8');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function feedback_stars(int $rating): string
{
  $rating = max(0, min(5, $rating));
  return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle ?? 'Customer Feedback - AutoNexus') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/feedback/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="management feedback-management">
      <header class="management-header">
        <div>
          <h2>Customer Feedback</h2>
          <p class="management-subtitle">Review customer comments, ratings, and send replies from one place.</p>
        </div>
      </header>

      <form class="tools feedback-tools" method="get" action="<?= $B ?>/admin/admin-viewfeedback">
        <input type="text" class="search-input" id="searchInput" name="q"
          placeholder="Search by customer, service, branch, or comment..." value="<?= $q ?>" />

        <select class="status-filter" id="ratingFilter" name="rating">
          <option value="">All Ratings</option>
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= (string) $ratingF === (string) $i ? 'selected' : '' ?>><?= $i ?>/5</option>
          <?php endfor; ?>
        </select>

        <select class="status-filter" id="repliedFilter" name="replied">
          <option value="">All Replies</option>
          <option value="replied" <?= $repliedF === 'replied' ? 'selected' : '' ?>>Replied</option>
          <option value="notReplied" <?= $repliedF === 'notReplied' ? 'selected' : '' ?>>Not Replied</option>
        </select>

        <input class="feedback-date" type="date" id="dateFilter" name="date" value="<?= $dateF ?>" />

        <button type="submit" class="apply-btn">
          <i class="fa-solid fa-filter"></i>
          <span>Apply</span>
        </button>
      </form>

      <div class="cards-container" id="cardsContainer">
        <?php if (empty($feedbacks)): ?>
          <div class="empty-state-box">
            <i class="fa-regular fa-face-smile"></i>
            <span>No feedback found for the selected filters.</span>
          </div>
        <?php else: ?>
          <?php foreach ($feedbacks as $f): ?>
            <?php
            $rating = (int) $f['rating'];
            $isReplied = strtolower($f['replied_status'] ?? '') === 'replied';
            $replyText = $f['reply_text'] ?? '';

            if ($rating >= 4)
              $scoreClass = 'green';
            elseif ($rating == 3)
              $scoreClass = 'yellow';
            else
              $scoreClass = 'red';

            $createdDate = substr($f['created_at'], 0, 10);
            $createdFmt = (new DateTime($f['created_at']))->format('M j, Y');
            ?>
            <div class="card" data-rating="<?= $rating ?>" data-replied="<?= $isReplied ? 'true' : 'false' ?>"
              data-date="<?= htmlspecialchars($createdDate, ENT_QUOTES, 'UTF-8') ?>">

              <div class="card-header">
                <strong><?= e($f['customer_name']) ?></strong>
                <div class="rating">
                  <span class="rating-score <?= $scoreClass ?>"><?= $rating ?>/5</span>
                  <span class="stars"><?= feedback_stars($rating) ?></span>
                </div>
              </div>

              <p><strong>Service:</strong> <?= e($f['service_name']) ?></p>
              <p><strong>Branch:</strong> <?= e($f['branch_name']) ?></p>
              <p><strong>Date:</strong> <?= e($createdFmt) ?></p>
              <p class="feedback-text"><?= nl2br(e($f['comment'] ?? '')) ?></p>

              <?php if ($isReplied): ?>
                <p class="reply-status replied">Replied</p>
                <?php if ($replyText !== ''): ?>
                  <p class="reply-preview">
                    <strong>Your Reply:</strong><br>
                    <?= nl2br(e($replyText)) ?>
                  </p>
                <?php endif; ?>
              <?php else: ?>
                <p class="reply-status not-replied">Not replied yet</p>
              <?php endif; ?>

              <!-- Reply form -->
              <form class="reply-form" method="post" action="<?= $B ?>/admin/admin-viewfeedback/reply">
                <input type="hidden" name="feedback_id" value="<?= (int) $f['feedback_id'] ?>">

                <textarea name="reply_text" rows="2" placeholder="Type your reply to this customer..."
                  required><?= htmlspecialchars($replyText) ?></textarea>


                <button type="submit" class="reply-btn">
                  <i class="fa-regular fa-paper-plane"></i>
                  <?= $isReplied ? 'Update Reply' : 'Send Reply' ?>
                </button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script>
    // optional: auto-submit on filter change
    document.addEventListener('DOMContentLoaded', () => {
      ['ratingFilter', 'repliedFilter', 'dateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.form) {
          el.addEventListener('change', () => el.form.submit());
        }
      });
    });


  </script>


</body>

</html>