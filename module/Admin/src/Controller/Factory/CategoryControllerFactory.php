<?php

namespace Admin\Controller\Factory;

use Admin\Controller\CategoryController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CategoryControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $categoryService = $container->get(\Deal\Service\CategoryService::class);
        
        return new CategoryController($categoryService);
    }
}

