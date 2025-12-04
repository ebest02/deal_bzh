<?php

return [
    'controllers' => [
        'factories' => [
            Controller\DashboardController::class => Controller\Factory\DashboardControllerFactory::class,
            Controller\UserController::class => Controller\Factory\UserControllerFactory::class,
            Controller\DealController::class => Controller\Factory\DealControllerFactory::class,
            Controller\MessageController::class => Controller\Factory\MessageControllerFactory::class,
            Controller\CategoryController::class => Controller\Factory\CategoryControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => Controller\DashboardController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'users' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/users',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
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
                                        'action' => 'view',
                                    ],
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/edit/:id',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'deals' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/deals',
                            'defaults' => [
                                'controller' => Controller\DealController::class,
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
                                        'action' => 'view',
                                    ],
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                            'moderate' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/moderate/:id',
                                    'defaults' => [
                                        'action' => 'moderate',
                                    ],
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'messages' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/messages',
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
                                        'action' => 'view',
                                    ],
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'categories' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/categories',
                            'defaults' => [
                                'controller' => Controller\CategoryController::class,
                                'action' => 'index',
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
];

