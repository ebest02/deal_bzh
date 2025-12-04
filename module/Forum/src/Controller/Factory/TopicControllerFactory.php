<?php

namespace Forum\Controller\Factory;

use Forum\Controller\TopicController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TopicControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $topicService = $container->get(\Forum\Service\TopicService::class);
        $postService = $container->get(\Forum\Service\PostService::class);
        $forumService = $container->get(\Forum\Service\ForumService::class);
        $authService = $container->get(\User\Service\AuthService::class);
        
        return new TopicController($topicService, $postService, $forumService, $authService);
    }
}

