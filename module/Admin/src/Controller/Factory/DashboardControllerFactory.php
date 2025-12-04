<?php

namespace Admin\Controller\Factory;

use Admin\Controller\DashboardController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DashboardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userService = $container->get(\User\Service\UserService::class);
        $dealService = $container->get(\Deal\Service\DealService::class);
        $messageService = $container->get(\Message\Service\MessageService::class);
        $categoryService = $container->get(\Deal\Service\CategoryService::class);
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        
        return new DashboardController($userService, $dealService, $messageService, $categoryService, $adapter);
    }
}

