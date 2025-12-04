<?php

namespace Application\Service;

use Laminas\Db\Adapter\Exception\RuntimeException as DbRuntimeException;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use PDOException;

class DatabaseErrorHandler
{
    public static function isDatabaseError(\Throwable $exception): bool
    {
        return $exception instanceof DbRuntimeException ||
               $exception instanceof InvalidQueryException ||
               $exception instanceof PDOException ||
               strpos($exception->getMessage(), 'SQLSTATE') !== false ||
               strpos($exception->getMessage(), 'database') !== false ||
               strpos($exception->getMessage(), 'connection') !== false ||
               strpos($exception->getMessage(), 'Access denied') !== false ||
               strpos($exception->getMessage(), 'Unknown database') !== false ||
               strpos($exception->getMessage(), 'Table') !== false && strpos($exception->getMessage(), "doesn't exist") !== false;
    }

    public static function getDatabaseErrorMessage(\Throwable $exception): string
    {
        $message = $exception->getMessage();
        
        if (strpos($message, 'Access denied') !== false) {
            return 'Identifiants de connexion à la base de données incorrects.';
        }
        
        if (strpos($message, 'Unknown database') !== false) {
            return 'La base de données n\'existe pas. Veuillez créer la base de données.';
        }
        
        if (strpos($message, "doesn't exist") !== false) {
            return 'Les tables de la base de données n\'existent pas. Veuillez exécuter le script SQL de création.';
        }
        
        if (strpos($message, 'connection') !== false || strpos($message, 'SQLSTATE') !== false) {
            return 'Impossible de se connecter à la base de données. Vérifiez que le serveur MySQL/MariaDB est démarré.';
        }
        
        return 'Erreur de base de données. Veuillez vérifier la configuration.';
    }
}

