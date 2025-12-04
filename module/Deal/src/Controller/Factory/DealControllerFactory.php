<?php

namespace Deal\Controller\Factory;

use Deal\Controller\DealController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DealControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dealService = $container->get(\Deal\Service\DealService::class);
        $categoryService = $container->get(\Deal\Service\CategoryService::class);
        $favoriteService = $container->get(\Deal\Service\FavoriteService::class);
        $authService = $container->get(\User\Service\AuthService::class);
        
        return new DealController($dealService, $categoryService, $favoriteService, $authService);
    }
}

