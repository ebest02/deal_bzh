<?php

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module
{
    public function getConfig(): array
    {
        $configFile = __DIR__ . '/config/module.config.php';
        
        if (!file_exists($configFile)) {
            throw new \RuntimeException(
                sprintf('Configuration file not found: %s', $configFile)
            );
        }
        
        if (!is_readable($configFile)) {
            throw new \RuntimeException(
                sprintf('Configuration file is not readable: %s', $configFile)
            );
        }
        
        $config = include $configFile;
        
        if ($config === false) {
            throw new \RuntimeException(
                sprintf('Failed to include configuration file: %s', $configFile)
            );
        }
        
        if (!is_array($config)) {
            throw new \RuntimeException(
                sprintf('Configuration file must return an array, got %s: %s', gettype($config), $configFile)
            );
        }
        
        return $config;
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        
        // Initialiser la session si disponible
        try {
            $sessionManager = $serviceManager->get(SessionManager::class);
        } catch (\Exception $e) {
            // Session non disponible, continuer sans
        }
        
        // Capturer les erreurs et exceptions
        $eventManager = $application->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError']);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'onRenderError']);
    }
    
    public function onDispatchError(MvcEvent $e): void
    {
        $exception = $e->getParam('exception');
        if ($exception) {
            $this->logError($e->getApplication()->getServiceManager(), $exception);
        }
    }
    
    public function onRenderError(MvcEvent $e): void
    {
        $exception = $e->getParam('exception');
        if ($exception) {
            $this->logError($e->getApplication()->getServiceManager(), $exception);
        }
    }
    
    protected function logError($serviceManager, $exception): void
    {
        try {
            if ($serviceManager->has(\Application\Service\Logger::class)) {
                $logger = $serviceManager->get(\Application\Service\Logger::class);
                $logger->logException($exception);
            } else {
                // Logger direct si le service n'est pas disponible
                $logFile = __DIR__ . '/../../logs/app.log';
                $message = sprintf(
                    "[%s] [ERROR] Exception: %s in %s:%d - %s\n%s\n",
                    date('Y-m-d H:i:s'),
                    get_class($exception),
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage(),
                    $exception->getTraceAsString()
                );
                file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
            }
        } catch (\Exception $e) {
            // Si le logger échoue, on écrit directement dans le fichier d'erreur PHP
            error_log(sprintf(
                'Logger error: %s - Original exception: %s',
                $e->getMessage(),
                $exception->getMessage()
            ));
        }
    }
}
