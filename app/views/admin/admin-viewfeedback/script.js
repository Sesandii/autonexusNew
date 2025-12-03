// // document.addEventListener('DOMContentLoaded', () => {
// //   const searchInput = document.getElementById('searchInput');
// //   const ratingFilter = document.getElementById('ratingFilter');
// //   const repliedFilter = document.getElementById('repliedFilter');
// //   const dateFilter = document.getElementById('dateFilter');
// //   const cardsContainer = document.getElementById('cardsContainer');
// //   const cards = cardsContainer.querySelectorAll('.card');

// //   function filterCards() {
// //     const searchText = searchInput.value.toLowerCase();
// //     const selectedRating = ratingFilter.value;
// //     const selectedReplied = repliedFilter.value;
// //     const selectedDate = dateFilter.value; // yyyy-mm-dd or empty

// //     cards.forEach(card => {
// //       const name = card.querySelector('strong').textContent.toLowerCase();
// //       const service = card.querySelector('p strong').nextSibling.textContent.toLowerCase();
// //       const feedbackText = card.querySelector('.feedback-text').textContent.toLowerCase();
// //       const rating = card.dataset.rating;
// //       const replied = card.dataset.replied === 'true';
// //       const date = card.dataset.date; // yyyy-mm-dd

// //       // Text search (name, service, feedback)
// //       const matchesSearch =
// //         name.includes(searchText) ||
// //         service.includes(searchText) ||
// //         feedbackText.includes(searchText);

// //       // Rating filter
// //       const matchesRating = selectedRating === 'all' || rating === selectedRating;

// //       // Replied filter
// //       const matchesReplied =
// //         selectedReplied === 'all' ||
// //         (selectedReplied === 'replied' && replied) ||
// //         (selectedReplied === 'notReplied' && !replied);

// //       // Date filter (exact match or empty)
// //       const matchesDate = !selectedDate || date === selectedDate;

// //       if (matchesSearch && matchesRating && matchesReplied && matchesDate) {
// //         card.style.display = 'flex';
// //       } else {
// //         card.style.display = 'none';
// //       }
// //     });
// //   }

// //   searchInput.addEventListener('input', filterCards);
// //   ratingFilter.addEventListener('change', filterCards);
// //   repliedFilter.addEventListener('change', filterCards);
// //   dateFilter.addEventListener('change', filterCards);
// // });


// <?php
// /** @var array       $feedbacks */
// /** @var array       $filters */
// /** @var float|null  $avgRating */
// /** @var string      $current */

// $current = $current ?? 'feedback';
// $B = rtrim(BASE_URL, '/');

// $q        = htmlspecialchars($filters['q']       ?? '', ENT_QUOTES, 'UTF-8');
// $ratingF  = $filters['rating']  ?? '';
// $repliedF = $filters['replied'] ?? '';
// $dateF    = htmlspecialchars($filters['date']    ?? '', ENT_QUOTES, 'UTF-8');

// function feedback_stars(int $rating): string {
//     $rating = max(0, min(5, $rating));
//     return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
// }
// ?>
// <!DOCTYPE html>
// <html lang="en">
// <head>
//   <meta charset="UTF-8" />
//   <meta name="viewport" content="width=device-width, initial-scale=1" />
//   <title><?= htmlspecialchars($pageTitle ?? 'Customer Feedback - AutoNexus') ?></title>

//   <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
//   <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/feedback/style.css">
//   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
// </head>
// <body>
// <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

// <main class="main-content">
//   <section class="feedback-section">
//     <h2>Customer Feedback Management</h2>

//     <?php if ($avgRating !== null): ?>
//       <p style="margin-bottom:10px;font-size:14px;">
//         Average Rating:
//         <strong><?= number_format($avgRating, 1) ?>/5</strong>
//         (<?= count($feedbacks) ?> reviews)
//       </p>
//     <?php endif; ?>

//     <!-- Filters -->
//     <form class="filters" method="get" action="<?= $B ?>/admin/admin-viewfeedback">
//       <input
//         type="text"
//         id="searchInput"
//         name="q"
//         placeholder="Search (customer, branch, service, comment)..."
//         value="<?= $q ?>"
//       />

//       <select id="ratingFilter" name="rating">
//         <option value="">All Ratings</option>
//         <?php for ($i = 5; $i >= 1; $i--): ?>
//           <option value="<?= $i ?>" <?= (string)$ratingF === (string)$i ? 'selected' : '' ?>>
//             <?= $i ?>/5
//           </option>
//         <?php endfor; ?>
//       </select>

//       <select id="repliedFilter" name="replied">
//         <option value="">All</option>
//         <option value="replied"    <?= $repliedF === 'replied' ? 'selected' : '' ?>>Replied</option>
//         <option value="notReplied" <?= $repliedF === 'notReplied' ? 'selected' : '' ?>>Not Replied</option>
//       </select>

//       <input type="date" id="dateFilter" name="date" value="<?= $dateF ?>" />

//       <button type="submit" class="reply-btn" style="margin-left:8px;">
//         <i class="fa-solid fa-magnifying-glass"></i> Apply
//       </button>
//     </form>

//     <!-- Cards -->
//     <div class="cards-container" id="cardsContainer">
//       <?php if (empty($feedbacks)): ?>
//         <p style="margin-top:20px;font-size:14px;color:#6b7280;">
//           No feedback found for the selected filters.
//         </p>
//       <?php else: ?>
//         <?php foreach ($feedbacks as $f): ?>
//           <?php
//             $rating    = (int)$f['rating'];
//             $isReplied = strtolower($f['replied_status'] ?? '') === 'replied';
//             $replyText = $f['reply_text'] ?? '';

//             if     ($rating >= 4) $scoreClass = 'green';
//             elseif ($rating == 3) $scoreClass = 'yellow';
//             else                  $scoreClass = 'red';

//             $createdDate = substr($f['created_at'], 0, 10);
//             $createdFmt  = (new DateTime($f['created_at']))->format('M j, Y');
//           ?>
//           <div class="card"
//                data-rating="<?= $rating ?>"
//                data-replied="<?= $isReplied ? 'true' : 'false' ?>"
//                data-date="<?= htmlspecialchars($createdDate, ENT_QUOTES, 'UTF-8') ?>">

//             <div class="card-header">
//               <strong><?= htmlspecialchars($f['customer_name']) ?></strong>
//               <div class="rating">
//                 <span class="rating-score <?= $scoreClass ?>"><?= $rating ?>/5</span>
//                 <span class="stars"><?= feedback_stars($rating) ?></span>
//               </div>
//             </div>

//             <p><strong>Service:</strong> <?= htmlspecialchars($f['service_name']) ?></p>
//             <p><strong>Branch:</strong> <?= htmlspecialchars($f['branch_name']) ?></p>
//             <p><strong>Date:</strong> <?= htmlspecialchars($createdFmt) ?></p>
//             <p class="feedback-text"><?= nl2br(htmlspecialchars($f['comment'] ?? '')) ?></p>

//             <?php if ($isReplied): ?>
//               <p class="reply-status replied">Replied</p>
//               <?php if ($replyText !== ''): ?>
//                 <p style="font-size:13px;margin-top:4px;">
//                   <strong>Your Reply:</strong><br>
//                   <?= nl2br(htmlspecialchars($replyText)) ?>
//                 </p>
//               <?php endif; ?>
//             <?php else: ?>
//               <p class="reply-status not-replied">Not replied yet</p>
//             <?php endif; ?>

//             <!-- Reply form -->
//             <form class="reply-form"
//                   method="post"
//                   action="<?= $B ?>/admin/admin-viewfeedback/reply"
//                   style="margin-top:8px;">
//               <input type="hidden" name="feedback_id" value="<?= (int)$f['feedback_id'] ?>">

//               <textarea
//                 name="reply_text"
//                 rows="2"
//                 placeholder="Type your reply to this customer..."
//                 style="width:100%;resize:vertical;font-size:13px;padding:6px;border-radius:8px;border:1px solid #d1d5db;"
//                 required><?= htmlspecialchars($replyText) ?></textarea>

//               <button type="submit" class="reply-btn" style="margin-top:6px;">
//                 <i class="fa-regular fa-paper-plane"></i>
//                 <?= $isReplied ? 'Update Reply' : 'Send Reply' ?>
//               </button>
//             </form>
//           </div>
//         <?php endforeach; ?>
//       <?php endif; ?>
//     </div>
//   </section>
// </main>

// <script>
// // optional: auto-submit on filter change
// document.addEventListener('DOMContentLoaded', () => {
//   ['ratingFilter','repliedFilter','dateFilter'].forEach(id => {
//     const el = document.getElementById(id);
//     if (el && el.form) {
//       el.addEventListener('change', () => el.form.submit());
//     }
//   });
// });


// </script>


// </body>
// </html>
