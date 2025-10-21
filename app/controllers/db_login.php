<?php
// db_login.php
declare(strict_types=1);

require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';
require_once __DIR__ . '/../core/roles.php';
require_once __DIR__ . '/../core/csrf.php'; // ← (see section 2)

// Ensure a session exists
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = 'Please use the login form.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

// Basic request sanity checks
$ctype = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($ctype, 'application/x-www-form-urlencoded') === false
    && stripos($ctype, 'multipart/form-data') === false) {
    $_SESSION['flash'] = 'Unsupported request.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

// CSRF validation
if (!csrf_check($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = 'Your session expired. Please try again.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

// Normalize input
$emailRaw = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$email = strtolower(trim($emailRaw));

// Server-side input validation
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    $_SESSION['flash'] = 'Invalid credentials'; // keep generic
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

// For passwords, don’t leak exact rules — but ensure it’s not empty and not absurdly large
if ($password === '' || strlen($password) > 1024) {
    $_SESSION['flash'] = 'Invalid credentials';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}

// --- Rate limit / lockout (by IP and email) ---
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$windowSeconds = 15 * 60;   // 15-minute window
$maxFailures   = 5;

try {
    $pdo = db();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table once (safe no-op if already created) — comment out after first run if you prefer migrations
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            ip VARCHAR(45) NOT NULL,
            attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            success TINYINT(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Count recent failures
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS failures
        FROM login_attempts
        WHERE success = 0
          AND (email = :email OR ip = :ip)
          AND attempted_at >= (NOW() - INTERVAL :win SECOND)
    ");
    $stmt->execute([
        'email' => $email,
        'ip'    => $ip,
        'win'   => $windowSeconds,
    ]);
    $failures = (int)$stmt->fetchColumn();

    if ($failures >= $maxFailures) {
        $_SESSION['flash'] = 'Too many attempts. Try again later.';
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Fetch user (case-insensitive on email)
    $stmt = $pdo->prepare('
        SELECT user_id, first_name, last_name, email, username, password_hash, role, status
        FROM users
        WHERE LOWER(email) = :email
        LIMIT 1
    ');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify credentials (keep timing roughly consistent)
    $ok = false;
    if ($user && isset($user['password_hash'])) {
        $ok = password_verify($password, $user['password_hash']);
    } else {
        // Dummy hash verify to keep timing similar when user not found
        password_verify($password, password_hash('dummy', PASSWORD_DEFAULT));
    }

    // If not ok or no user, record failure and bounce
    if (!$ok || !$user) {
        $log = $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:email, :ip, 0)');
        $log->execute(['email' => $email, 'ip' => $ip]);

        $_SESSION['flash'] = 'Invalid credentials';
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Account status check
    if (($user['status'] ?? '') !== 'active') {
        $log = $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:email, :ip, 0)');
        $log->execute(['email' => $email, 'ip' => $ip]);

        $_SESSION['flash'] = 'Account is ' . $user['status'];
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Rehash if needed (algorithm upgrades)
    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE users SET password_hash = :h WHERE user_id = :id');
        $upd->execute(['h' => $newHash, 'id' => (int)$user['user_id']]);
    }

    // Record success + optionally clear old failures for this pair
    $log = $pdo->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:email, :ip, 1)');
    $log->execute(['email' => $email, 'ip' => $ip]);

    $clear = $pdo->prepare("
        DELETE FROM login_attempts
        WHERE (email = :email OR ip = :ip) AND success = 0
          AND attempted_at < (NOW() - INTERVAL :win SECOND)
    ");
    $clear->execute(['email' => $email, 'ip' => $ip, 'win' => $windowSeconds]);

    // Harden the session
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'user_id'    => (int)$user['user_id'],
        'first_name' => (string)$user['first_name'],
        'last_name'  => (string)$user['last_name'],
        'email'      => (string)$user['email'],
        'username'   => (string)$user['username'],
        'role'       => (string)$user['role'],
    ];

    // OPTIONAL: rotate CSRF token after login
    csrf_rotate();

    // Redirect by role (your helper)
    redirect_by_role($user['role']);
    exit;

} catch (Throwable $e) {
    // Avoid leaking internals
    $_SESSION['flash'] = 'Server error. Please try again.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/login');
    exit;
}
