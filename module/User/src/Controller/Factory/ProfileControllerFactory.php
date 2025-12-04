<?php

namespace User\Controller\Factory;

use User\Controller\ProfileController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProfileControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $authService = $container->get(\User\Service\AuthService::class);
        $userService = $container->get(\User\Service\UserService::class);
        
        return new ProfileController($authService, $userService);
    }
}

