<?php

return [
    'modules' => [
        'Laminas\Router',
        'Laminas\Validator',
        'Laminas\Form',
        'Laminas\InputFilter',
        'Laminas\Filter',
        'Laminas\Hydrator',
        'Laminas\Db',
        'Laminas\Session',
        'Laminas\Cache',
        'Laminas\I18n',
        'Laminas\Mail',
        'Laminas\Navigation',
        'Laminas\Paginator',
        'Laminas\Cache\Storage\Adapter\Filesystem',
        'Application',
        'User',
        'Deal',
        'Message',
        'Admin',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{{,*.}global,{,*.}local}.php',
        ],
        'config_cache_enabled' => false,
        'config_cache_key' => 'application.config.cache',
        'module_map_cache_enabled' => false,
        'module_map_cache_key' => 'application.module.cache',
        'cache_dir' => 'data/cache',
        'check_dependencies' => true,
    ],
];

