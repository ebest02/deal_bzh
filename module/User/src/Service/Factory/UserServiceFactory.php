<?php

namespace User\Service\Factory;

use User\Service\UserService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        return new UserService($adapter);
    }
}

