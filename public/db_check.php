<?php
declare(strict_types=1);

// Minimal bootstrap
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once CONFIG_PATH . '/config.php';
require_once APP_ROOT . '/core/Database.php';



header('Content-Type: text/plain');

echo "== DB CONFIG ==\n";
echo "HOST: " . (defined('DB_HOST') ? DB_HOST : '(undefined)') . "\n";
echo "PORT: " . (defined('DB_PORT') ? DB_PORT : '(undefined)') . "\n";
echo "NAME: " . (defined('DB_NAME') ? DB_NAME : '(undefined)') . "\n";
echo "USER: " . (defined('DB_USER') ? DB_USER : '(undefined)') . "\n";
echo "SOCKET: " . (defined('DB_SOCKET') ? (DB_SOCKET ?: '(empty)') : '(undefined)') . "\n\n";

try {
    $pdo = db();
    echo "✅ Connected!\n";

    // Server version
    $ver = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL VERSION: $ver\n\n";

    // Check tables
    echo "== TABLES ==\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
    if (!$tables) {
        echo "No tables found.\n";
    } else {
        foreach ($tables as $t) echo "- {$t[0]}\n";
    }

    echo "\n== COUNTS ==\n";
    foreach (['users','managers'] as $t) {
        try {
            $cnt = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo "$t: $cnt\n";
        } catch (Throwable $e) {
            echo "$t: ERROR: {$e->getMessage()}\n";
        }
    }

    echo "\n== SAMPLE ROWS ==\n";
    foreach (['users','managers'] as $t) {
        try {
            $rows = $pdo->query("SELECT * FROM `$t` LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            echo ">>> $t (up to 3 rows)\n";
            if (!$rows) { echo "(none)\n"; continue; }
            foreach ($rows as $r) echo json_encode($r, JSON_PRETTY_PRINT) . "\n";
        } catch (Throwable $e) {
            echo ">>> $t: ERROR: {$e->getMessage()}\n";
        }
        echo "\n";
    }

} catch (Throwable $e) {
    echo "❌ Connection FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
