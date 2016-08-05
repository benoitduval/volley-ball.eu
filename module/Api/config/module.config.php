<?php

namespace Api;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Api\Controller\AuthController;

return [
    'router' => [
        'routes' => [
            // 'api' => [
            //     'type'    => Segment::class,
            //     'options' => [
            //         'route'    => '/api/auth[/:action]',
            //         'constraints' => [
            //             'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            //         ],
            //         'defaults' => [
            //             'controller'    => Controller\AuthController::class,
            //             'action'        => 'signin',
            //         ],
            //     ],
            // ],
        ],
    ],

    // 'service_manager' => [
    //     'factories' => [
    //         AuthenticationService::class => AuthenticationServiceFactory::class,
    //     ],
    // ],

    // 'controllers' => [
    //     'factories' => [
    //         Controller\AuthController::class => InvokableFactory::class,
    //     ],
    // ],

    // 'controllers' => [
    //     'abstract_factories' => [
    //         \Application\Factory\ControllerFactory::class
    //     ],
    // ],

    'view_manager' => [
        
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
