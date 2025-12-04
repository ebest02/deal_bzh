<?php

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module
{
    public function getConfig(): array
    {
<<<<<<< HEAD
        $configFile = __DIR__ . '/config/module.config.php';
        if (!file_exists($configFile)) {
            throw new \RuntimeException(
                sprintf('Configuration file not found: %s', $configFile)
            );
        }
        $config = include $configFile;
        if (!is_array($config)) {
            throw new \RuntimeException(
                sprintf('Configuration file must return an array: %s', $configFile)
            );
        }
        return $config;
=======
        return include __DIR__ . '/config/module.config.php';
>>>>>>> ba7dd9a (issues solved)
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        
        // Initialiser la session
        $sessionManager = $serviceManager->get(SessionManager::class);
    }
}

