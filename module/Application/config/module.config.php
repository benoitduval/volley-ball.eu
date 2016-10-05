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
            'welcome' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/welcome',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'welcome',
                    ],
                ],
            ],
            'event' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/event[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-z][a-z_-]*',
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
                        'action'   => '[a-z][a-z_-]*',
                        'id'       => '[0-9]+',
                        'response' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GuestController::class,
                        'action'        => 'response',
                    ],
                ],
            ],
            'guest-response' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/guest/response[/:id[/:response]]',
                    'constraints' => [
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
                    'route'    => '/group[/:action[/:id[/:userId]]]',
                    'constraints' => [
                        'action' => '[a-z][a-z_-]*',
                        'id'     => '[0-9]+',
                        'userId' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GroupController::class,
                        'action'        => 'create',
                    ],
                ],
            ],
            'recurent' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/recurent[/:action[/:id[/:recurentId]]]',
                    'constraints' => [
                        'action' => '[a-z][a-z_-]*',
                        'id'     => '[0-9]+',
                        'recurentId'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\RecurentController::class,
                        'action'        => 'create',
                    ],
                ],
            ],
            'group-welcome' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/welcome-to[/:brand]',
                    'constraints' => [
                        'brand' => '[a-z\-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\GroupController::class,
                        'action'        => 'welcome',
                    ],
                ],
            ],
            // 'group-user-delete' => [
            //     'type'    => Segment::class,
            //     'options' => [
            //         'route'    => '/group/:id/:action/:userId',
            //         'constraints' => [
            //             'id'     => '[0-9]+',
            //             'userId' => '[0-9]+',
            //         ],
            //         'defaults' => [
            //             'controller'    => Controller\GroupController::class,
            //             'action'        => 'deleteUser',
            //         ],
            //     ],
            // ],
            'auth' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/auth[/:action[/:url]]',
                    'constraints' => [
                        'action' => '[a-z][a-z_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\AuthController::class,
                        'action'        => 'signin',
                    ],
                ],
            ],
            'match' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/match[/:action[/:id]]',
                    'constraints' => [
                        'action'   => '[a-z][a-z_-]*',
                        'id'       => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\MatchController::class,
                        'action'        => 'create',
                    ],
                ],
            ],
            'search' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/search',
                    'defaults' => [
                        'controller'    => Controller\SearchController::class,
                        'action'        => 'index',
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
                'recurent' => [
                    'options' => [
                        'route'    => 'recurent [--verbose|-v]',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action'     => 'recurent',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
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
