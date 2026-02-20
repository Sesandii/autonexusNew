<?php
// db_login.php (no CSRF)
declare(strict_types=1);

require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';
require_once __DIR__ . '/../core/roles.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$base = rtrim(BASE_URL, '/');

// Only accept POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $_SESSION['flash'] = 'Please use the login form.';
    header('Location: ' . $base . '/login');
    exit;
}

// Normalize input
$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['flash'] = 'Invalid credentials';
    header('Location: ' . $base . '/login');
    exit;
}

$ip            = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$windowSeconds = 15 * 60;   // 15 minutes
$maxFailures   = 5;

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Simple attempts table (create once; safe no-op if exists)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            ip VARCHAR(45) NOT NULL,
            attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            success TINYINT(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Block if too many recent failures
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM login_attempts
        WHERE success = 0
          AND (email = :email OR ip = :ip)
          AND attempted_at >= (NOW() - INTERVAL :win SECOND)
    ");
    $stmt->execute(['email'=>$email,'ip'=>$ip,'win'=>$windowSeconds]);
    if ((int)$stmt->fetchColumn() >= $maxFailures) {
        $_SESSION['flash'] = 'Too many attempts. Try again later.';
        header('Location: ' . $base . '/login');
        exit;
    }

    // Fetch user (case-insensitive)
    $stmt = $pdo->prepare('
        SELECT user_id, first_name, last_name, email, username, password_hash, role, status
        FROM users
        WHERE LOWER(email) = :email
        LIMIT 1
    ');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Constant-ish timing: verify even if user missing
    $ok = false;
    if ($user && isset($user['password_hash'])) {
        $ok = password_verify($password, $user['password_hash']);
    } else {
        password_verify($password, password_hash('dummy', PASSWORD_DEFAULT));
    }

    if (!$ok || !$user) {
        $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:e,:i,0)')
            ->execute(['e'=>$email,'i'=>$ip]);
        $_SESSION['flash'] = 'Invalid credentials';
        header('Location: ' . $base . '/login');
        exit;
    }

    if (($user['status'] ?? '') !== 'active') {
        $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:e,:i,0)')
            ->execute(['e'=>$email,'i'=>$ip]);
        $_SESSION['flash'] = 'Account is ' . $user['status'];
        header('Location: ' . $base . '/login');
        exit;
    }

    // Rehash if algo updated
    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = :h WHERE user_id = :id')
            ->execute(['h'=>$newHash, 'id'=>(int)$user['user_id']]);
    }

    // Log success
    $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:e,:i,1)')
        ->execute(['e'=>$email,'i'=>$ip]);

    // Session
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'user_id'    => (int)$user['user_id'],
        'first_name' => (string)$user['first_name'],
        'last_name'  => (string)$user['last_name'],
        'email'      => (string)$user['email'],
        'username'   => (string)$user['username'],
        'role'       => (string)$user['role'],
    ];

    // Redirect by role
    redirect_by_role($user['role']);
    exit;

} catch (Throwable $e) {
    $_SESSION['flash'] = 'Server error. Please try again.';
    header('Location: ' . $base . '/login');
    exit;
}
