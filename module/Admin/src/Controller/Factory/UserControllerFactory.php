<?php

namespace Admin\Controller\Factory;

use Admin\Controller\UserController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userService = $container->get(\User\Service\UserService::class);
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        
        return new UserController($userService, $adapter);
    }
}

