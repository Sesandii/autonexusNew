//gender checkboxes in register page


ALTER TABLE users
ADD COLUMN sex ENUM('male','female') NULL AFTER last_name;

//handler

<?php
// app/controllers/register_handler.php (no CSRF)
declare(strict_types=1);

require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$base = rtrim(BASE_URL, '/');

// Only accept POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $_SESSION['flash'] = 'Please use the registration form.';
    header('Location: ' . $base . '/register');
    exit;
}

/* ---------------- Collect & normalize ---------------- */
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = strtolower(trim($_POST['email'] ?? ''));
$phone      = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
$alt_phone  = preg_replace('/\D+/', '', $_POST['alt_phone'] ?? '');
$street     = trim($_POST['street'] ?? '');
$city       = trim($_POST['city'] ?? '');
$state      = trim($_POST['state'] ?? '');
$username   = trim($_POST['username'] ?? '');
$sexRaw     = $_POST['sex'] ?? '';
$sex        = is_array($sexRaw) ? strtolower(trim((string)($sexRaw[0] ?? ''))) : strtolower(trim((string)$sexRaw));
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirm_password'] ?? '';

/* ---------------- Validate ---------------- */
$errors = [];
$nameRe = "/^[A-Za-z][A-Za-z\s'.-]{1,49}$/";

if ($first_name === '' || $last_name === '') {
    $errors[] = 'First and last name are required.';
} else {
    if (!preg_match($nameRe, $first_name)) $errors[] = 'First name must be 2–50 letters (A–Z), spaces, (.\'-).';
    if (!preg_match($nameRe, $last_name))  $errors[] = 'Last name must be 2–50 letters (A–Z), spaces, (.\'-).';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    $errors[] = 'Enter a valid email address (≤254 chars).';
}

if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) {
    $errors[] = 'Phone number must be exactly 10 digits.';
}
if ($alt_phone !== '' && !preg_match('/^\d{10}$/', $alt_phone)) {
    $errors[] = 'Alternate phone must be exactly 10 digits.';
}

if (strlen($street) > 120) $errors[] = 'Street is too long (max 120).';
if (strlen($city)   > 100) $errors[] = 'City is too long (max 100).';
if (strlen($state)  > 100) $errors[] = 'State is too long (max 100).';

if (!in_array($sex, ['male', 'female'], true)) {
    $errors[] = 'Please select a gender.';
}

// Username fallback from email local-part, capped 30
$usernameToUse = $username !== '' ? $username : strtok($email, '@');
$usernameToUse = substr($usernameToUse, 0, 30);
if ($usernameToUse === '' || !preg_match('/^[A-Za-z0-9_.]{3,30}$/', $usernameToUse)) {
    $errors[] = 'Username must be 3–30 chars (letters, numbers, _ or .).';
}

// Password rules
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
} else {
    $lacks = [];
    if (!preg_match('/[a-z]/', $password)) $lacks[] = 'lowercase';
    if (!preg_match('/[A-Z]/', $password)) $lacks[] = 'uppercase';
    if (!preg_match('/\d/',   $password)) $lacks[] = 'number';
    if (!preg_match('/[^A-Za-z0-9]/', $password)) $lacks[] = 'symbol';
    if ($lacks) $errors[] = 'Password must include ' . implode(', ', $lacks) . '.';
}
if ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}

if ($errors) {
    $_SESSION['flash'] = implode(' ', $errors);
    header('Location: ' . $base . '/register');
    exit;
}

/* ---------------- Persist (schema-aware) ---------------- */
try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    // Enforce uniqueness (before insert)
    $check = $pdo->prepare('SELECT user_id FROM users WHERE email = :email OR username = :username LIMIT 1');
    $check->execute(['email' => $email, 'username' => $usernameToUse]);
    if ($check->fetch()) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Email or username already exists.';
        header('Location: ' . $base . '/register');
        exit;
    }

    $hash   = password_hash($password, PASSWORD_DEFAULT);
    $role   = 'customer';
    $status = 'active'; // or 'pending' if you need approval

    // Detect users table columns
    $userCols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN, 0);
    $hasStreet        = in_array('street', $userCols, true);
    $hasStreetAddress = in_array('street_address', $userCols, true);
    $hasSex           = in_array('sex', $userCols, true);
    $hasCreatedAt     = in_array('created_at', $userCols, true);

    // Build dynamic INSERT for users
    $insertCols    = ['first_name','last_name','username','email','password_hash','phone','alt_phone','city','state','role','status'];
    $placeHolders  = [':first_name',':last_name',':username',':email',':password_hash',':phone',':alt_phone',':city',':state',':role',':status'];

    if ($hasStreet) {
        $insertCols[]   = 'street';
        $placeHolders[] = ':street';
    } elseif ($hasStreetAddress) {
        $insertCols[]   = 'street_address';
        $placeHolders[] = ':street';
    }

    if ($hasSex) {
        $insertCols[]   = 'sex';
        $placeHolders[] = ':sex';
    }

    if ($hasCreatedAt) {
        $insertCols[]   = 'created_at';
        $placeHolders[] = 'NOW()';
    }

    $sqlUsers = 'INSERT INTO users ('.implode(',', $insertCols).') VALUES ('.implode(',', $placeHolders).')';
    $insertUser = $pdo->prepare($sqlUsers);

    $params = [
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'username'      => $usernameToUse,
        'email'         => $email,
        'password_hash' => $hash,
        'phone'         => $phone,
        'alt_phone'     => $alt_phone !== '' ? $alt_phone : null,
        'city'          => $city   !== '' ? $city   : null,
        'state'         => $state  !== '' ? $state  : null,
        'role'          => $role,
        'status'        => $status,
    ];
    if ($hasStreet || $hasStreetAddress) {
        $params['street'] = $street !== '' ? $street : null; // maps to whichever col exists
    }
    if ($hasSex) {
        $params['sex'] = $sex;
    }

    $insertUser->execute($params);
    $userId = (int)$pdo->lastInsertId();

    // Insert into customers (schema-aware)
    $custCols = $pdo->query("SHOW COLUMNS FROM customers")->fetchAll(PDO::FETCH_COLUMN, 0);
    $hasCustCreated = in_array('created_at', $custCols, true);

    $customerCode = 'CUS-' . str_pad((string)$userId, 6, '0', STR_PAD_LEFT);

    $cCols = ['user_id','customer_code'];
    $cVals = [':user_id',':customer_code'];
    if ($hasCustCreated) {
        $cCols[] = 'created_at';
        $cVals[] = 'NOW()';
    }

    $sqlCust = 'INSERT INTO customers ('.implode(',', $cCols).') VALUES ('.implode(',', $cVals).')';
    $insertCustomer = $pdo->prepare($sqlCust);
    $insertCustomer->execute([
        'user_id'       => $userId,
        'customer_code' => $customerCode,
    ]);

    $pdo->commit();

    $_SESSION['flash'] = 'Account created. Please login.';
    header('Location: ' . $base . '/login');
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();

    // 23000 = integrity constraint (e.g., duplicate key on unique email/username)
    if ((int)$e->getCode() === 23000) {
        $_SESSION['flash'] = 'Email or username already exists.';
    } else {
        // Uncomment to log exact DB error in development:
        // error_log('[register_handler] '.$e->getMessage());
        $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    }
    header('Location: ' . $base . '/register');
    exit;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    // error_log('[register_handler] '.$e->getMessage());
    $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    header('Location: ' . $base . '/register');
    exit;
}



//app/views/register/index

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

      <div class="field">
        <span style="display:block; margin-bottom:8px; font-weight:600;">Gender</span>
        <label style="margin-right:16px; display:inline-flex; align-items:center; gap:6px;">
          <input type="checkbox" name="sex" value="male" required>
          Male
        </label>
        <label style="display:inline-flex; align-items:center; gap:6px;">
          <input type="checkbox" name="sex" value="female" required>
          Female
        </label>
      </div>

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

  <script src="<?= htmlspecialchars($base) ?>/app/views/register/assests/js/script.js"></script>
  <script>
    // Keep checkbox UX but allow only one gender selection.
    document.addEventListener('DOMContentLoaded', function () {
      var boxes = Array.from(document.querySelectorAll('input[name="sex"]'));
      boxes.forEach(function (box) {
        box.addEventListener('change', function () {
          if (box.checked) {
            boxes.forEach(function (other) {
              if (other !== box) other.checked = false;
            });
          }
          var anyChecked = boxes.some(function (b) { return b.checked; });
          boxes.forEach(function (b) { b.required = !anyChecked; });
        });
      });
    });
  </script>

</body>
</html>



//

