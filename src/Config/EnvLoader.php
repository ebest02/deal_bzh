<?php

namespace Application\Config;

/**
 * Chargeur de variables d'environnement depuis .env
 */
class EnvLoader
{
    protected static $loaded = false;

    /**
     * Charge les variables d'environnement depuis le fichier .env
     */
    public static function load(string $envFile = null): void
    {
        if (self::$loaded) {
            return;
        }

        if ($envFile === null) {
            $envFile = dirname(dirname(__DIR__)) . '/.env';
        }

        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Ignorer les lignes vides
            if (empty(trim($line))) {
                continue;
            }

            // Parser la ligne KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Supprimer les guillemets
                $value = trim($value, '"\'');
                
                // Ne pas écraser les variables d'environnement existantes
                if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Récupère une variable d'environnement
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}

