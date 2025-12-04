<?php

namespace User\Controller\Factory;

use User\Controller\RatingController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RatingControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $ratingService = $container->get(\User\Service\RatingService::class);
        $authService = $container->get(\User\Service\AuthService::class);
        $dealService = $container->get(\Deal\Service\DealService::class);
        
        return new RatingController($ratingService, $authService, $dealService);
    }
}

