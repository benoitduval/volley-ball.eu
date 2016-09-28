<?php

namespace Api;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Api\Controller;

return [
    'router' => [
        'routes' => [
            'grant-user' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/user/grant/:groupId/:userId/:status',
                    'constraints' => [
                        'groupId' => '[0-9]*',
                        'userId' => '[0-9]*',
                        'status' => '[0-1]{1}',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'grant',
                    ],
                ],
            ],
            'user-display' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/user/display/:display',
                    'constraints' => [
                        'display' => '[1-2]{1}',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'display',
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
