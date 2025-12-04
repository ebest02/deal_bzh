<?php

namespace Message\Service\Factory;

use Message\Service\MessageService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MessageServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        return new MessageService($adapter);
    }
}

