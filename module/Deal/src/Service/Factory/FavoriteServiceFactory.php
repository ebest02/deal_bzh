<?php

namespace Deal\Service\Factory;

use Deal\Service\FavoriteService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FavoriteServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        return new FavoriteService($adapter);
    }
}

