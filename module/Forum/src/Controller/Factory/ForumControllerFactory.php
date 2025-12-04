<?php

namespace Forum\Controller\Factory;

use Forum\Controller\ForumController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ForumControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $forumService = $container->get(\Forum\Service\ForumService::class);
        $topicService = $container->get(\Forum\Service\TopicService::class);
        
        return new ForumController($forumService, $topicService);
    }
}

