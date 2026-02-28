<?php
// app/core/Database.php
// Ensure DB_* constants are available

require_once CONFIG_PATH . '/config.php';




/**
 * Returns a singleton PDO connection.
 */
function db(): \PDO {
    static $pdo;
    if ($pdo instanceof \PDO) return $pdo;

    if (!defined('DB_PORT'))    define('DB_PORT', '3306');
    if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

    if (defined('DB_SOCKET') && DB_SOCKET) {
        $dsn = 'mysql:unix_socket=' . DB_SOCKET . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    } else {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    }

    try {
        $pdo = new \PDO($dsn, DB_USER, DB_PASS, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (\PDOException $e) {
        http_response_code(500);
        echo "<pre style='font:14px/1.4 monospace'>Database connection failed.\n\n"
           . "Host: ".(defined('DB_HOST')?DB_HOST:'(undefined)')."\n"
           . "Port: ".(defined('DB_PORT')?DB_PORT:'(undefined)')."\n"
           . "DB: ".(defined('DB_NAME')?DB_NAME:'(undefined)')."\n"
           . "User: ".(defined('DB_USER')?DB_USER:'(undefined)')."\n\n"
           . "PDOException: ".$e->getMessage()."</pre>";
        exit;
    }
}
