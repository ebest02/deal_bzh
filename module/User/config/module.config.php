<?php

return [
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\ProfileController::class => Controller\Factory\ProfileControllerFactory::class,
            Controller\RatingController::class => Controller\Factory\RatingControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'user' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'register' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/register',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'register',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'profile' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/profile[/:id]',
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'view',
                            ],
                            'constraints' => [
                                'id' => '\d+',
                            ],
                        ],
                    ],
            'edit-profile' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/edit-profile',
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'edit',
                    ],
                ],
            ],
            'rating' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/rating',
                    'defaults' => [
                        'controller' => Controller\RatingController::class,
                        'action' => 'create',
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
            Service\UserService::class => Service\Factory\UserServiceFactory::class,
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,
            Service\RatingService::class => Service\Factory\RatingServiceFactory::class,
            \Laminas\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
        ],
    ],
];

