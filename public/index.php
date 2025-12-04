<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// Charger les variables d'environnement depuis .env
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../src/Config/EnvLoader.php';
    \Application\Config\EnvLoader::load();
}

// Configuration des logs PHP
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Rediriger toutes les erreurs PHP vers le fichier de log
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/error.log');

// Configuration de l'affichage des erreurs selon l'environnement
$appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production');
$displayErrors = ($appEnv === 'development' || (getenv('APP_DEBUG') ?: $_ENV['APP_DEBUG'] ?? 'false') === 'true') ? '1' : '0';
ini_set('display_errors', $displayErrors);
ini_set('display_startup_errors', $displayErrors);
error_reporting(E_ALL);

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    throw new RuntimeException('Vendor directory not found. Please run composer install.');
}

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
    );
}

$appConfig = include 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, include 'config/development.config.php');
}

Application::init($appConfig)->run();

