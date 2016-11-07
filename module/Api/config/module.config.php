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
                        'display' => '[1-3]{1}',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'display',
                    ],
                ],
            ],
            'guest' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/guest/response/:eventId/:response',
                    'constraints' => [
                        'response' => '[1-3]{1}',
                        'eventId' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GuestController::class,
                        'action'        => 'response',
                    ],
                ],
            ],
            'notif' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/user/params/:id/:status',
                    'constraints' => [
                        'id' => '[0-9]*',
                        'status' => '[1-2]{1}',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'params',
                    ],
                ],
            ],
            'comment' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/comment/:eventId/:groupId',
                    'constraints' => [
                        'eventId' => '[0-9]*',
                        'groupId' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\CommentController::class,
                        'action'        => 'index',
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
