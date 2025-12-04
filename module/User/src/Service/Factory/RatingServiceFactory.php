<?php

namespace User\Service\Factory;

use User\Service\RatingService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RatingServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        $userService = $container->get(\User\Service\UserService::class);
        
        return new RatingService($adapter, $userService);
    }
}

