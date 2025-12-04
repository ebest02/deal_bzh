<?php

namespace User\Service\Factory;

use User\Service\AuthService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(\Laminas\Authentication\AuthenticationService::class);
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        $userService = $container->get(\User\Service\UserService::class);
        
        return new AuthService($authService, $adapter, $userService);
    }
}

