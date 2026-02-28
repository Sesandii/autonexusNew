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
