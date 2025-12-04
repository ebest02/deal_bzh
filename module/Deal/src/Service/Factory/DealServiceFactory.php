<?php

namespace Deal\Service\Factory;

use Deal\Service\DealService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DealServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        return new DealService($adapter);
    }
}

