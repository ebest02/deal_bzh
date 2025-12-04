<?php

return [
    'controllers' => [
        'factories' => [
            Controller\ForumController::class => Controller\Factory\ForumControllerFactory::class,
            Controller\TopicController::class => Controller\Factory\TopicControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'forum' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/forum',
                    'defaults' => [
                        'controller' => Controller\ForumController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'category' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/category/:slug',
                            'defaults' => [
                                'controller' => Controller\ForumController::class,
                                'action' => 'category',
                            ],
                            'constraints' => [
                                'slug' => '[a-z0-9-]+',
                            ],
                        ],
                    ],
                    'topic' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/topic/:id',
                            'defaults' => [
                                'controller' => Controller\TopicController::class,
                                'action' => 'view',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'create' => [
                                'type' => \Laminas\Router\Http\Literal::class,
                                'options' => [
                                    'route' => '/create',
                                    'defaults' => [
                                        'action' => 'create',
                                    ],
                                ],
                            ],
                            'reply' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/reply/:topicId',
                                    'defaults' => [
                                        'action' => 'reply',
                                    ],
                                    'constraints' => [
                                        'topicId' => '\d+',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\ForumService::class => Service\Factory\ForumServiceFactory::class,
            Service\TopicService::class => Service\Factory\TopicServiceFactory::class,
            Service\PostService::class => Service\Factory\PostServiceFactory::class,
        ],
    ],
];

