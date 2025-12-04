<?php

namespace Admin;

use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        // Vérifier l'accès admin sur toutes les routes admin
        $sharedEventManager->attach(
            \Laminas\Mvc\Controller\AbstractActionController::class,
            \Laminas\Mvc\MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );
    }

    public function onDispatch(MvcEvent $e): void
    {
        $controller = $e->getTarget();
        $routeMatch = $e->getRouteMatch();

        if (!$routeMatch) {
            return;
        }

        $routeName = $routeMatch->getMatchedRouteName();

        // Vérifier si c'est une route admin
        if (strpos($routeName, 'admin') === 0) {
            $authService = $e->getApplication()->getServiceManager()->get(\User\Service\AuthService::class);
            
            if (!$authService->hasIdentity() || !$authService->getIdentity()->isAdmin()) {
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', '/user/login');
                $response->setStatusCode(302);
                $response->sendHeaders();
                exit;
            }
        }
    }
}

