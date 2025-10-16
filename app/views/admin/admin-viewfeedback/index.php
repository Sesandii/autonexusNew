<?php $current = 'feedback'; // highlights “Service Progress” in sidebar ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Feedback - AutoNexus</title>

  <link rel="stylesheet" href="../admin-sidebar/styles.css">   <!-- fixed sidebar styles -->
  <link rel="stylesheet" href="style.css">                    <!-- this page’s styles -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>
<body>

 <?php include(__DIR__ . '/../admin-sidebar/sidebar.php'); ?>



    <!-- Main Content -->
    <main class="main-content">
      

      <section class="feedback-section">
        <h2>Customer Feedback Management</h2>

        <div class="filters">
          <input type="text" id="searchInput" placeholder="Search feedback..." />
          <select id="ratingFilter">
            <option value="all">All Ratings</option>
            <option value="5">5/5</option>
            <option value="4">4/5</option>
            <option value="3">3/5</option>
            <option value="2">2/5</option>
            <option value="1">1/5</option>
          </select>
          <select id="repliedFilter">
            <option value="all">All</option>
            <option value="replied">Replied</option>
            <option value="notReplied">Not Replied</option>
          </select>
          <input type="date" id="dateFilter" />
        </div>

        <div class="cards-container" id="cardsContainer">
          <div class="card" data-rating="5" data-replied="true" data-date="2023-11-05">
            <div class="card-header">
              <strong>John Smith</strong>
              <div class="rating">
                <span class="rating-score green">5/5</span>
                <span class="stars">★★★★★</span>
              </div>
            </div>
            <p><strong>Service:</strong> Oil Change</p>
            <p><strong>Date:</strong> Nov 5, 2023</p>
            <p class="feedback-text">Great service! The staff was friendly and the work was done quickly and efficiently.</p>
            <p class="reply-status replied">Replied</p>
            <button class="reply-btn">Reply</button>
          </div>

          <div class="card" data-rating="2" data-replied="false" data-date="2023-11-04">
            <div class="card-header">
              <strong>Sarah Williams</strong>
              <div class="rating">
                <span class="rating-score red">2/5</span>
                <span class="stars">★★☆☆☆</span>
              </div>
            </div>
            <p><strong>Service:</strong> Brake Inspection</p>
            <p><strong>Date:</strong> Nov 4, 2023</p>
            <p class="feedback-text">The service took much longer than expected. I had to wait for over 2 hours for what should have been a quick inspection.</p>
            <p class="reply-status not-replied">Not replied yet</p>
            <button class="reply-btn">Reply</button>
          </div>

          <div class="card" data-rating="4" data-replied="true" data-date="2023-11-03">
            <div class="card-header">
              <strong>Michael Johnson</strong>
              <div class="rating">
                <span class="rating-score green">4/5</span>
                <span class="stars">★★★★☆</span>
              </div>
            </div>
            <p><strong>Service:</strong> Tire Rotation</p>
            <p><strong>Date:</strong> Nov 3, 2023</p>
            <p class="feedback-text">Good service overall. The mechanic explained everything clearly, but the waiting area could use some improvement.</p>
            <p class="reply-status replied">Replied</p>
            <button class="reply-btn">Reply</button>
          </div>

          <div class="card" data-rating="1" data-replied="false" data-date="2023-11-02">
            <div class="card-header">
              <strong>Emily Davis</strong>
              <div class="rating">
                <span class="rating-score red">1/5</span>
                <span class="stars">★☆☆☆☆</span>
              </div>
            </div>
            <p><strong>Service:</strong> Full Service</p>
            <p><strong>Date:</strong> Nov 2, 2023</p>
            <p class="feedback-text">Very disappointed with the service. My car still has the same issue after the repair, and the staff was not helpful when I called to follow up.</p>
            <p class="reply-status not-replied">Not replied yet</p>
            <button class="reply-btn">Reply</button>
          </div>

          <div class="card" data-rating="3" data-replied="false" data-date="2023-11-01">
            <div class="card-header">
              <strong>Robert Brown</strong>
              <div class="rating">
                <span class="rating-score yellow">3/5</span>
                <span class="stars">★★★☆☆</span>
              </div>
            </div>
            <p><strong>Service:</strong> Engine Diagnostic</p>
            <p><strong>Date:</strong> Nov 1, 2023</p>
            <p class="feedback-text">Average service. The diagnostic was accurate but the price was higher than quoted.</p>
            <p class="reply-status not-replied">Not replied yet</p>
            <button class="reply-btn">Reply</button>
          </div>
        </div>
      </section>
    </main>
 

  <script src="feedback.js"></script>
</body>
</html>
