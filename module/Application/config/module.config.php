<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [

    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'event' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/event[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\EventController::class,
                        'action'        => 'detail',
                    ],
                ],
            ],
            'guest' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/guest[/:action[/:id[/:response]]]',
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                        'response' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GuestController::class,
                        'action'        => 'response',
                    ],
                ],
            ],
            'group' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/group[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GroupController::class,
                        'action'        => 'create',
                    ],
                ],
            ],
            'group-welcome' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/welcome-to[/:brand]',
                    'constraints' => [
                        'brand' => '[a-zA-Z\-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GroupController::class,
                        'action'        => 'welcome',
                    ],
                ],
            ],
            'auth' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/auth[/:action[/:url]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\AuthController::class,
                        'action'        => 'signin',
                    ],
                ],
            ],
            'example' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/example',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'example',
                    ],
                ],
            ],
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'migration' => [
                    'options' => [
                        'route'    => 'migration [--verbose|-v]',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action'     => 'migration',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'invokable' => [
            Service\Map::class,
        ],
        'factories' => [
            Model\UserTableGateway::class         => Factory\TableGatewayFactory::class,
            Model\GroupTableGateway::class        => Factory\TableGatewayFactory::class,
            Model\MatchTableGateway::class        => Factory\TableGatewayFactory::class,
            Model\EventTableGateway::class        => Factory\TableGatewayFactory::class,
            Model\GuestTableGateway::class        => Factory\TableGatewayFactory::class,
            Model\JoinTableGateway::class         => Factory\TableGatewayFactory::class,
            Model\RecurentTableGateway::class     => Factory\TableGatewayFactory::class,
            Model\CommentTableGateway::class      => Factory\TableGatewayFactory::class,
            Model\UserGroupTableGateway::class    => Factory\TableGatewayFactory::class,
            Model\NotificationTableGateway::class => Factory\TableGatewayFactory::class,

            Service\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\MailService::class           => Service\Factory\MailServiceFactory::class,
            Service\Map::class                   => Service\Factory\MapServiceFactory::class,
        ],
    ],

    'controllers' => [
        'abstract_factories' => [
            Factory\ControllerFactory::class
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
