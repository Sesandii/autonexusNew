<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base = realpath(__DIR__ . '/../app/views');
$path = $base . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin-viewmanagers' . DIRECTORY_SEPARATOR . 'index.php';

header('Content-Type: text/plain');

echo "BASE: $base\n";
echo "TARGET: $path\n\n";

echo "file_exists(TARGET): " . (file_exists($path) ? 'YES' : 'NO') . "\n";
echo "is_file(TARGET):     " . (is_file($path) ? 'YES' : 'NO') . "\n\n";

$adminDir = $base . DIRECTORY_SEPARATOR . 'admin';
$mgrDir   = $adminDir . DIRECTORY_SEPARATOR . 'admin-viewmanagers';

echo "== LIST app/views ==\n";
print_r(scandir($base));

echo "\n== LIST app/views/admin ==\n";
if (is_dir($adminDir)) print_r(scandir($adminDir)); else echo "(missing)\n";

echo "\n== LIST app/views/admin/admin-viewmanagers ==\n";
if (is_dir($mgrDir)) print_r(scandir($mgrDir)); else echo "(missing)\n";
