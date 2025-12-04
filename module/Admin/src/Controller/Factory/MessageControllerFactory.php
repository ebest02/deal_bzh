<?php

namespace Admin\Controller\Factory;

use Admin\Controller\MessageController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MessageControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $messageService = $container->get(\Message\Service\MessageService::class);
        
        return new MessageController($messageService);
    }
}

