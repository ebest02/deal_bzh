<?php

return [
    'controllers' => [
        'factories' => [
            Controller\DealController::class => Controller\Factory\DealControllerFactory::class,
            Controller\CategoryController::class => Controller\Factory\CategoryControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'deal' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/deal',
                    'defaults' => [
                        'controller' => Controller\DealController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'create' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/create',
                            'defaults' => [
                                'controller' => Controller\DealController::class,
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'view' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/:id',
                            'defaults' => [
                                'controller' => Controller\DealController::class,
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
                                'controller' => Controller\DealController::class,
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/delete/:id',
                            'defaults' => [
                                'controller' => Controller\DealController::class,
                                'action' => 'delete',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                    ],
                    'favorite' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/favorite/:id',
                            'defaults' => [
                                'controller' => Controller\DealController::class,
                                'action' => 'favorite',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                    ],
                ],
            ],
            'category' => [
                'type' => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route' => '/category/:slug',
                    'defaults' => [
                        'controller' => Controller\CategoryController::class,
                        'action' => 'view',
                    ],
                    'constraints' => [
                        'slug' => '[a-z0-9-]+',
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
            Service\DealService::class => Service\Factory\DealServiceFactory::class,
            Service\CategoryService::class => Service\Factory\CategoryServiceFactory::class,
            Service\FavoriteService::class => Service\Factory\FavoriteServiceFactory::class,
        ],
    ],
];

