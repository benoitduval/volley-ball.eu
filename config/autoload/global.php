<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'db' => [
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=album;host=127.0.0.1',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'routes' => [
        'public' => [
            'Application\Controller\AuthController' => [
                'signin',
                'signup'
            ],
            'Application\Controller\IndexController' => [
                'index'
            ],
            'Application\Controller\GroupController' => [
                'public'
            ],
        ]
    ]
    // 'service_manager' => array(
    //     'factories' => array(
    //         'Zend\Db\Adapter\Adapter'
    //                 => 'Zend\Db\Adapter\AdapterServiceFactory',
    //     ),
    //     'abstract_factories' => array(
    //         'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
    //     )
    // ),
];
