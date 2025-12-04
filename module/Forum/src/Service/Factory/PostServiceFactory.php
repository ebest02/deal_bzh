<?php

namespace Forum\Service\Factory;

use Forum\Service\PostService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PostServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
        return new PostService($adapter);
    }
}

