<?php

namespace Deal\Controller\Factory;

use Deal\Controller\CategoryController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CategoryControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dealService = $container->get(\Deal\Service\DealService::class);
        $categoryService = $container->get(\Deal\Service\CategoryService::class);
        
        return new CategoryController($dealService, $categoryService);
    }
}

