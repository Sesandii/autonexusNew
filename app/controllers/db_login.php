<?php
// db_login.php
require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';

require_once __DIR__ . '/../core/roles.php';

// start session only if not already started (index.php starts one)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = 'Please use the login form.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');   // ← fix URL
    exit;
}

$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['flash'] = 'Invalid credentials';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');   // ← fix URL
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('
        SELECT user_id, first_name, last_name, email, username, password_hash, role, status
        FROM users WHERE email = :email LIMIT 1
    ');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
} catch (Throwable $e) {
    $_SESSION['flash'] = 'Server error. Please try again.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');   // ← fix URL
    exit;
}

if (!$user) {
    $_SESSION['flash'] = 'No such email';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    $_SESSION['flash'] = 'Wrong password';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password_hash = :h WHERE user_id = :id');
    $upd->execute(['h' => $newHash, 'id' => $user['user_id']]);
}

if ($user['status'] !== 'active') {
    $_SESSION['flash'] = 'Account is ' . $user['status'];
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'user_id'    => (int)$user['user_id'],
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
    'email'      => $user['email'],
    'username'   => $user['username'],
    'role'       => $user['role'],
];

// Send them based on role (your helper)
redirect_by_role($user['role']);
