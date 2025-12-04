<?php

namespace User\Controller\Factory;

use User\Controller\AuthController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(\User\Service\AuthService::class);
        $userService = $container->get(\User\Service\UserService::class);
        
        return new AuthController($authService, $userService);
    }
}

