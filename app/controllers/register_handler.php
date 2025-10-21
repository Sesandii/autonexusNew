<?php
// app/controllers/register_handler.php
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

/* ------------ Collect & normalize ------------ */
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = strtolower(trim($_POST['email'] ?? ''));
$phone      = preg_replace('/\D+/', '', $_POST['phone'] ?? '');          // keep digits only
$alt_phone  = preg_replace('/\D+/', '', $_POST['alt_phone'] ?? '');      // optional
$street     = trim($_POST['street'] ?? '');
$city       = trim($_POST['city'] ?? '');
$state      = trim($_POST['state'] ?? '');
$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirm_password'] ?? '';

/* ------------ Validation rules ------------ */
$errors = [];

// Names: required, 2–50 letters + common punctuation/spaces
$nameRe = "/^[A-Za-z][A-Za-z\s'.-]{1,49}$/";
if ($first_name === '' || $last_name === '') {
    $errors[] = 'First and last name are required.';
} else {
    if (!preg_match($nameRe, $first_name)) $errors[] = 'First name must be 2–50 letters (A–Z), spaces, (.\'-).';
    if (!preg_match($nameRe, $last_name))  $errors[] = 'Last name must be 2–50 letters (A–Z), spaces, (.\'-).';
}

// Email: required, valid, max length
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    $errors[] = 'Enter a valid email address (≤254 chars).';
}

// Phone: required, exactly 10 digits
if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) {
    $errors[] = 'Phone number must be exactly 10 digits.';
}

// Alt phone: optional, if present must be 10 digits
if ($alt_phone !== '' && !preg_match('/^\d{10}$/', $alt_phone)) {
    $errors[] = 'Alternate phone must be exactly 10 digits.';
}

// Addresses: soft limits
if (strlen($street) > 120) $errors[] = 'Street is too long (max 120).';
if (strlen($city)   > 100) $errors[] = 'City is too long (max 100).';
if (strlen($state)  > 100) $errors[] = 'State is too long (max 100).';

// Username: optional; if empty we’ll derive from email local-part
$usernameToUse = $username !== '' ? $username : strtok($email, '@');
$usernameToUse = substr($usernameToUse, 0, 30); // cap to 30
if ($usernameToUse === '') {
    $errors[] = 'Could not derive a username from your email—please enter one.';
} else {
    if (!preg_match('/^[A-Za-z0-9_.]{3,30}$/', $usernameToUse)) {
        $errors[] = 'Username must be 3–30 chars (letters, numbers, _ or .).';
    }
}

// Password: 8+ and include lower, upper, digit, symbol; must match
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

// Bail early on errors
if ($errors) {
    $_SESSION['flash'] = implode(' ', $errors);
    header('Location: ' . $base . '/register');
    exit;
}

/* ------------ Persist ------------ */
try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    // Uniqueness check (email or username)
    $check = $pdo->prepare(
        'SELECT user_id FROM users WHERE email = :email OR username = :username LIMIT 1'
    );
    $check->execute(['email' => $email, 'username' => $usernameToUse]);
    if ($check->fetch()) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Email or username already exists.';
        header('Location: ' . $base . '/register');
        exit;
    }

    // Hash password
    $hash   = password_hash($password, PASSWORD_DEFAULT);
    $role   = 'customer';
    $status = 'active'; // change to 'pending' if you need approval

    // NOTE: Column name fix — your schema uses `street`, not `street_address`
    $insertUser = $pdo->prepare(
        'INSERT INTO users
         (first_name, last_name, username, email, password_hash, phone, alt_phone,
          street, city, state, role, status, created_at)
         VALUES
         (:first_name, :last_name, :username, :email, :password_hash, :phone, :alt_phone,
          :street, :city, :state, :role, :status, NOW())'
    );

    $insertUser->execute([
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'username'      => $usernameToUse,
        'email'         => $email,
        'password_hash' => $hash,
        'phone'         => $phone,
        'alt_phone'     => $alt_phone !== '' ? $alt_phone : null,
        'street'        => $street !== '' ? $street : null,
        'city'          => $city   !== '' ? $city   : null,
        'state'         => $state  !== '' ? $state  : null,
        'role'          => $role,
        'status'        => $status,
    ]);

    $userId = (int)$pdo->lastInsertId();

    // Customer row
    $customerCode = 'CUS-' . str_pad((string)$userId, 6, '0', STR_PAD_LEFT);
    $insertCustomer = $pdo->prepare(
        'INSERT INTO customers (user_id, customer_code, created_at)
         VALUES (:user_id, :customer_code, NOW())'
    );
    $insertCustomer->execute([
        'user_id'       => $userId,
        'customer_code' => $customerCode,
    ]);

    $pdo->commit();

    $_SESSION['flash'] = 'Account created. Please login.';
    header('Location: ' . $base . '/login');
    exit;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // error_log($e->getMessage());
    $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    header('Location: ' . $base . '/register');
    exit;
}
