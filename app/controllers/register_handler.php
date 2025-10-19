<!-- app/controllers/register_handler.php -->
<?php
require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // for flash messages
}

$base = rtrim(BASE_URL, '/');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $_SESSION['flash'] = 'Please use the registration form.';
    header('Location: ' . $base . '/register');
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = strtolower(trim($_POST['email'] ?? ''));
$phone      = trim($_POST['phone'] ?? '');
$alt_phone  = trim($_POST['alt_phone'] ?? '');
$street     = trim($_POST['street'] ?? '');
$city       = trim($_POST['city'] ?? '');
$state      = trim($_POST['state'] ?? '');
$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirm_password'] ?? '';

$errors = [];
if ($first_name === '' || $last_name === '') $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';
if ($phone === '') $errors[] = 'Phone is required.';

if ($errors) {
    $_SESSION['flash'] = implode(' ', $errors);
    header('Location: ' . $base . '/register');
    exit;
}

// If username empty, use email (or just the part before @ if you prefer)
if ($username === '') $username = $email; // or: substr($email, 0, strpos($email, '@'))

try {
    $pdo = db();
    $pdo->beginTransaction();

    // 1) Check uniqueness
    $check = $pdo->prepare(
        'SELECT user_id FROM users WHERE email = :email OR username = :username LIMIT 1'
    );
    $check->execute(['email' => $email, 'username' => $username]);
    if ($check->fetch()) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Email or username already exists';
        header('Location: ' . $base . '/register');
        exit;
    }

    // 2) Insert user
    $hash   = password_hash($password, PASSWORD_DEFAULT);
    $role   = 'customer';
    $status = 'active'; // <= change to 'pending' if you want approval flow

    $insertUser = $pdo->prepare(
        'INSERT INTO users
         (first_name, last_name, username, email, password_hash, phone, alt_phone,
          street_address, city, state, role, status, created_at)
         VALUES
         (:first_name, :last_name, :username, :email, :password_hash, :phone, :alt_phone,
          :street, :city, :state, :role, :status, NOW())'
    );

    $insertUser->execute([
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'username'      => $username ?: $email,
        'email'         => $email,
        'password_hash' => $hash,
        'phone'         => $phone,
        'alt_phone'     => $alt_phone ?: null,
        'street'        => $street ?: null,
        'city'          => $city ?: null,
        'state'         => $state ?: null,
        'role'          => $role,
        'status'        => $status,
    ]);

    $userId = (int) $pdo->lastInsertId();

    // 3) Insert customer row (linked to users.user_id)
    //    Generate a simple code; adjust format as you like
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
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    // error_log($e->getMessage());
    $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    header('Location: ' . $base . '/register');
    exit;
}
 catch (Throwable $e) {
    // You can log $e->getMessage() to a file for debugging
    $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    header('Location: ' . $base . '/register');
    exit;
}
