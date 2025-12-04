<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== DIAGNOSTIC PHP/APACHE ===\n\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server API: " . php_sapi_name() . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "Working Directory: " . getcwd() . "\n";
echo "\n=== PERMISSIONS ===\n";
echo "index.php readable: " . (is_readable(__DIR__ . '/index.php') ? 'YES' : 'NO') . "\n";
echo "index.php writable: " . (is_writable(__DIR__ . '/index.php') ? 'YES' : 'NO') . "\n";
echo "Parent dir readable: " . (is_readable(__DIR__ . '/..') ? 'YES' : 'NO') . "\n";
echo "\n=== AUTOLOAD ===\n";
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "vendor/autoload.php: EXISTS\n";
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "Autoload loaded: YES\n";
} else {
    echo "vendor/autoload.php: NOT FOUND\n";
}
echo "\n=== CONFIG ===\n";
if (file_exists(__DIR__ . '/../config/application.config.php')) {
    echo "application.config.php: EXISTS\n";
    $config = include __DIR__ . '/../config/application.config.php';
    echo "Config loaded: " . (is_array($config) ? 'YES' : 'NO') . "\n";
} else {
    echo "application.config.php: NOT FOUND\n";
}
echo "\n=== ERRORS ===\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . error_reporting() . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "error_log: " . ini_get('error_log') . "\n";

