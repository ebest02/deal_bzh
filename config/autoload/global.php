<?php

return [
    'db' => [
        'driver' => 'Pdo_Mysql',
        'database' => 'deal_bzh',
        'username' => 'root',
        'password' => '',
        'hostname' => 'localhost',
        'charset' => 'utf8mb4',
    ],
    'session_config' => [
        'options' => [
            'name' => 'deal_bzh_session',
            'cookie_lifetime' => 7200,
            'gc_maxlifetime' => 7200,
        ],
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'session_validators' => [
        \Laminas\Session\Validator\RemoteAddr::class,
        \Laminas\Session\Validator\HttpUserAgent::class,
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../../module/Application/view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../../module/Application/view/error/404.phtml',
            'error/index' => __DIR__ . '/../../module/Application/view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../../module/Application/view',
            __DIR__ . '/../../module/User/view',
            __DIR__ . '/../../module/Deal/view',
            __DIR__ . '/../../module/Message/view',
            __DIR__ . '/../../module/Admin/view',
            __DIR__ . '/../../module/Forum/view',
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => \Application\Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Accueil',
                'route' => 'home',
            ],
            [
                'label' => 'Annonces',
                'route' => 'deal',
            ],
            [
                'label' => 'Déposer une annonce',
                'route' => 'deal/create',
                'visible' => ['role' => 'user'],
            ],
            [
                'label' => 'Mes messages',
                'route' => 'message',
                'visible' => ['role' => 'user'],
            ],
            [
                'label' => 'Administration',
                'route' => 'admin',
                'visible' => ['role' => 'admin'],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Laminas\Db\Adapter\AdapterInterface::class => \Laminas\Db\Adapter\AdapterServiceFactory::class,
            \Laminas\Authentication\AuthenticationService::class => \User\Service\Factory\AuthenticationServiceFactory::class,
        ],
    ],
];

