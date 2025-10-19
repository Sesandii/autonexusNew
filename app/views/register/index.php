<?php
require_once CONFIG_PATH . '/config.php';

// Don't start session here if it's already started elsewhere.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$base = rtrim(BASE_URL, '/');

if (isset($_SESSION['flash'])) {
    echo '<script>alert(' . json_encode($_SESSION['flash']) . ');</script>';
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoNexus — Create Your Account</title>
  <!-- FIXED: assests → assets, and use $base -->
  <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/app/views/register/assests/css/styles.css?v=1" />
</head>
<body>

  <!-- Left / Hero (fixed) -->
  <section class="hero">
    <div class="hero-inner">
      <div class="brand-mark" aria-label="AUTONEXUS logo">
        <span class="brand-accent">AUTO</span><span class="brand-main">NEXUS</span>
        <div class="brand-sub">VEHICLE SERVICE</div>
      </div>

      <div class="hero-copy">
        <h1>Your Trusted Vehicle Service<br/>Partner</h1>
        <p class="subtitle">
          Join AutoNexus and experience seamless vehicle
          service management with our cutting-edge platform.
        </p>

        <ul class="bullets">
          <li><span class="num">1</span><h3>Easy appointment scheduling</h3></li>
          <li><span class="num">2</span><h3>Real-time service updates</h3></li>
          <li><span class="num">3</span><h3>Comprehensive service history</h3></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Right / Form -->
  <!-- FIXED: post to /register (router), not register_handler.php -->
  <section class="panel">
    <form class="card form" method="post" action="<?= htmlspecialchars($base) ?>/register">
      <div class="mini-brand" aria-hidden="true">
        <span class="brand-accent">AUTO</span><span class="brand-main">NEXUS</span>
      </div>
      <h2>Create Your Account</h2>
      <p class="muted">Join AutoNexus to manage your vehicle services</p>

      <div class="grid two">
        <label class="field with-icon">
          <span class="icon">👤</span>
          <input type="text" name="first_name" placeholder="First Name" required/>
        </label>
        <label class="field with-icon">
          <span class="icon">👤</span>
          <input type="text" name="last_name" placeholder="Last Name" required/>
        </label>
      </div>

      <label class="field with-icon">
        <span class="icon">✉️</span>
        <input type="email" name="email" placeholder="Email Address" required/>
      </label>

      <label class="field with-icon">
        <span class="icon">📞</span>
        <input type="tel" name="phone" placeholder="Phone Number" required/>
      </label>

      <label class="field with-icon">
        <span class="icon">📱</span>
        <input type="tel" name="alt_phone" placeholder="Alternate Phone Number (Optional)"/>
      </label>

      <label class="field with-icon">
        <span class="icon">🏠</span>
        <input type="text" name="street" placeholder="Street/House No."/>
      </label>

      <div class="grid two">
        <label class="field with-icon">
          <span class="icon">🏙️</span>
          <input type="text" name="city" placeholder="City/Town"/>
        </label>
        <label class="field with-icon">
          <span class="icon">🗺️</span>
          <input type="text" name="state" placeholder="State/Province"/>
        </label>
      </div>


      <label class="field with-icon">
        <span class="icon">👤</span>
        <input type="text" name="username" placeholder="Username (if not using email)"/>
      </label>

      <label class="field with-icon">
        <span class="icon">🔒</span>
        <input type="password" name="password" placeholder="Password" required/>
      </label>

      <label class="field with-icon">
        <span class="icon">🔒</span>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required/>
      </label>

      <button class="btn primary" type="submit">Sign Up</button>

      <!-- FIXED: login link to route -->
      <p class="footnote">Already have an account?
        <a href="<?= htmlspecialchars($base) ?>/login">Login</a>
      </p>
    </form>
  </section>
</body>
</html>