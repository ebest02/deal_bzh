<?php

/**
 * Configuration locale de l'application
 * 
 * Utilise les variables d'environnement depuis .env
 */

// Charger les variables d'environnement si disponible
if (file_exists(__DIR__ . '/../../.env') && class_exists(\Application\Config\EnvLoader::class)) {
    \Application\Config\EnvLoader::load();
}

// Fonction helper pour récupérer les variables d'environnement
$getEnv = function($key, $default = null) {
    return getenv($key) ?: ($_ENV[$key] ?? $_SERVER[$key] ?? $default);
};

return [
    'db' => [
        'driver' => 'Pdo_Mysql',
        'database' => $getEnv('DB_NAME', 'deal_bzh'),
        'username' => $getEnv('DB_USER', 'deal_bzh'),
        'password' => $getEnv('DB_PASSWORD', ''),
        'hostname' => $getEnv('DB_HOST', 'localhost'),
        'port' => (int)$getEnv('DB_PORT', 3306),
        'charset' => 'utf8mb4',
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ],
    ],
    'logging' => [
        'enabled' => true,
        'level' => $getEnv('LOG_LEVEL', 'INFO'),
    ],
];
