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

    // unique email/username
    $check = $pdo->prepare(
        'SELECT user_id FROM users WHERE email = :email OR username = :username LIMIT 1'
    );
    $check->execute(['email' => $email, 'username' => $username]);
    if ($check->fetch()) {
        $_SESSION['flash'] = 'Email or username already exists';
        header('Location: ' . $base . '/register');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role   = 'customer';
    $status = 'pending'; // change to 'active' if you want immediate login

    $insert = $pdo->prepare(
        'INSERT INTO users
         (first_name, last_name, username, email, password_hash, phone, alt_phone, street_address, city, state, role, status, created_at)
         VALUES
         (:first_name, :last_name, :username, :email, :password_hash, :phone, :alt_phone, :street, :city, :state, :role, :status, NOW())'
    );

    $insert->execute([
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'username'      => $username,
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

    $_SESSION['flash'] = 'Account created. Please login.';
    header('Location: ' . $base . '/login');
    exit;

} catch (Throwable $e) {
    // You can log $e->getMessage() to a file for debugging
    $_SESSION['flash'] = 'Server error while creating the account. Please try again.';
    header('Location: ' . $base . '/register');
    exit;
}
