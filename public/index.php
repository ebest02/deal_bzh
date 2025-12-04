<?php

use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

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

