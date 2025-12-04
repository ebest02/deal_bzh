<?php

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        
        // Initialiser la session
        $sessionManager = $serviceManager->get(SessionManager::class);
    }
}

