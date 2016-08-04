<?php

namespace Api;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'api' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/auth[/:action]',
                    'constraints' => [
                        'action' => '[a-z\-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\AuthController::class,
                        'action'        => 'signin',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
            'factories' => [
                Controller\AuthController::class => InvokableFactory::class,
            ],
        ],
    'view_manager' => [
        'template_path_stack' => [
            'api' => __DIR__ . '/../view',
        ],
    ],
];