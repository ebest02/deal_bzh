<?php

namespace Admin\Controller\Factory;

use Admin\Controller\DealController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DealControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $dealService = $container->get(\Deal\Service\DealService::class);
        
        return new DealController($dealService);
    }
}

