<?php

namespace User\Service\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(\Laminas\Session\SessionManager::class);
        $storage = new Session(null, null, $sessionManager);
        
        return new AuthenticationService($storage);
    }
}

