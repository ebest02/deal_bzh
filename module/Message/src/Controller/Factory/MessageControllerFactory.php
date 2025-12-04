<?php

namespace Message\Controller\Factory;

use Message\Controller\MessageController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MessageControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $messageService = $container->get(\Message\Service\MessageService::class);
        $authService = $container->get(\User\Service\AuthService::class);
        $dealService = $container->get(\Deal\Service\DealService::class);
        $userService = $container->get(\User\Service\UserService::class);
        
        return new MessageController($messageService, $authService, $dealService, $userService);
    }
}

