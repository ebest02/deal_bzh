<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// Configuration des logs PHP
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Rediriger toutes les erreurs PHP vers le fichier de log
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/error.log');
// Rediriger toutes les erreurs PHP vers le fichier de log
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/error.log');
// En production, désactiver l'affichage des erreurs
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
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

