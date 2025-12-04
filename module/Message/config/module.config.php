<?php

return [
    'controllers' => [
        'factories' => [
            Controller\MessageController::class => Controller\Factory\MessageControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'message' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/message',
                    'defaults' => [
                        'controller' => Controller\MessageController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/:id',
                            'defaults' => [
                                'controller' => Controller\MessageController::class,
                                'action' => 'view',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                    ],
                    'create' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/create',
                            'defaults' => [
                                'controller' => Controller\MessageController::class,
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'reply' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/reply/:id',
                            'defaults' => [
                                'controller' => Controller\MessageController::class,
                                'action' => 'reply',
                            ],
                            'constraints' => [
                                'id' => '\d+',
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
            Service\MessageService::class => Service\Factory\MessageServiceFactory::class,
        ],
    ],
];

