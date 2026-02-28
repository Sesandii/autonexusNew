<?php /** @var array $branches */ ?>
<?php $pageTitle = $title ?? 'AutoNexus'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/home.css" />
</head>
<body>
  <!-- NAVBAR -->
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="<?= rtrim(BASE_URL,'/') ?>/">
        <img src="<?= rtrim(BASE_URL,'/') ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo" class="brand-logo" />
        <span class="brand-text">AutoNexus</span>
      </a>

      <nav class="main-nav" id="mainNav">
        <a href="<?= rtrim(BASE_URL,'/') ?>/">Home</a>
        <a href="#services" id="servicesNavLink">Services</a>
        <a href="#how">How It Works</a>
        <a href="#benefits">Why Choose Us</a>
        <a href="#reviews">Reviews</a>
        <a href="#contact">Contact</a>
        <a href="<?= rtrim(BASE_URL,'/') ?>/login" class="btn-outline">Login</a>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
      <h1>Simplify Your Vehicle Maintenance</h1>
      <p class="lead">Book, track, and manage your vehicle services with ease.</p>
      <div class="hero-cta">
        <a href="<?= rtrim(BASE_URL,'/') ?>/login" class="btn-primary">Book a Service</a>
        <a href="<?= rtrim(BASE_URL,'/') ?>/login" class="btn-secondary">Login</a>
      </div>
    </div>
  </section>

  <!-- SERVICES -->
  <section id="services" class="section">
    <div class="container">
      <h2 class="section-title2">Our Services</h2>
      <p class="section-sub">We offer a comprehensive range of services to keep your vehicle running at its best.</p>

      <div class="services-grid" id="servicesGrid">
        <!-- (Optional) You can populate with JS from /assets/js/home.js -->
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how" class="section light">
    <div class="container">
      <h2 class="section-title2">How It Works</h2>
      <p class="section-sub">Our streamlined process makes vehicle maintenance simple and convenient.</p>

      <div class="how-grid">
        <div class="how-card">
          <div class="icon-circle">👥</div>
          <h3>Sign Up / Login</h3>
          <p>Create an account or log in to access our services.</p>
        </div>

        <div class="how-card">
          <div class="icon-circle">📅</div>
          <h3>Book a Service</h3>
          <p>Choose the service you need and select a convenient time.</p>
        </div>

        <div class="how-card">
          <div class="icon-circle">🔧</div>
          <h3>Track Your Progress</h3>
          <p>Monitor your vehicle's service status in real-time.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- BENEFITS -->
  <section id="benefits" class="section dark">
    <div class="container">
      <h2 class="section-title white">Why Choose AutoNexus</h2>
      <p class="section-sub white">We’re committed to providing the best service experience for your vehicle.</p>

      <div class="benefits-grid">
        <div class="benefit-card">
          <h4>Certified Mechanics</h4>
          <p>Our team consists of certified professionals with years of experience.</p>
        </div>
        <div class="benefit-card">
          <h4>Transparent Pricing</h4>
          <p>No hidden fees. We provide clear estimates before any work begins.</p>
        </div>
        <div class="benefit-card">
          <h4>Real-time Updates</h4>
          <p>Stay informed with instant updates about your service status.</p>
        </div>
        <div class="benefit-card">
          <h4>Quality Assurance</h4>
          <p>We guarantee the quality of our work with comprehensive checks.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section id="reviews" class="section">
    <div class="container">
      <h2 class="section-title2">Customer Reviews</h2>
      <p class="section-sub">Don't just take our word for it. Here's what our customers say.</p>

      <div class="testimonial-wrap">
        <button class="carousel-btn left" id="prevBtn" aria-label="Previous">&#10094;</button>
        <div class="testimonial" id="testimonial"></div>
        <button class="carousel-btn right" id="nextBtn" aria-label="Next">&#10095;</button>
      </div>

      <div class="dots" id="dots"></div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer id="contact" class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <img src="<?= rtrim(BASE_URL,'/') ?>/public/assets/img/logo.jpg" alt="AutoNexus" class="footer-logo" />
        <p>Simplifying vehicle maintenance through technology and exceptional service.</p>
      </div>

      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="<?= rtrim(BASE_URL,'/') ?>/">Home</a></li>
          <li><a href="#services" id="servicesFooterLink">Services</a></li>
          <li><a href="#how">How It Works</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <p>📞 (123) 456-7890</p>
        <p>✉️ support@autonexus.com</p>
        <p>📍 123 Service Road, Automotive City</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> AutoNexus. All rights reserved.</p>
    </div>
  </footer>

  <!-- Modal -->
  <div id="servicesModal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <button class="close-btn" aria-label="Close">&times;</button>
      <h2>Select Your Branch</h2>
      <p>Choose a branch to see its available services.</p>

      <select id="branchSelect" aria-label="Branch">
        <option value="" disabled selected>-- Select Branch --</option>
        <?php foreach ($branches as $b): ?>
          <option value="<?= htmlspecialchars($b['branch_code']) ?>">
            <?= htmlspecialchars($b['branch_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button id="goToServices" class="primary">Continue</button>
    </div>
  </div>

  <script>
    const BASE_URL = "<?= rtrim(BASE_URL,'/') ?>";
  </script>
  <script src="<?= rtrim(BASE_URL,'/') ?>/public/assets/js/home.js"></script>
</body>
</html>
