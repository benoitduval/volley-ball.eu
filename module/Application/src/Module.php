<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Application\TableGateway;
use Application\Service\AuthenticationService;

class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.0';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                TableGateway\User::class => function($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new TableGateway\User($tableGateway);
                },
                TableGateway\Comment::class => function($container) {
                    $tableGateway = $container->get(Model\CommentTableGateway::class);
                    return new TableGateway\Comment($tableGateway);
                },
                TableGateway\Group::class => function($container) {
                    $tableGateway = $container->get(Model\GroupTableGateway::class);
                    return new TableGateway\Group($tableGateway);
                },
                TableGateway\Badge::class => function($container) {
                    $tableGateway = $container->get(Model\BadgeTableGateway::class);
                    return new TableGateway\Badge($tableGateway);
                },
                TableGateway\Event::class => function($container) {
                    $tableGateway = $container->get(Model\EventTableGateway::class);
                    return new TableGateway\Event($tableGateway);
                },
                TableGateway\Guest::class => function($container) {
                    $tableGateway = $container->get(Model\GuestTableGateway::class);
                    return new TableGateway\Guest($tableGateway);
                },
                TableGateway\Join::class => function($container) {
                    $tableGateway = $container->get(Model\JoinTableGateway::class);
                    return new TableGateway\Join($tableGateway);
                },
                TableGateway\Notification::class => function($container) {
                    $tableGateway = $container->get(Model\NotificationTableGateway::class);
                    return new TableGateway\Notification($tableGateway);
                },
                TableGateway\Place::class => function($container) {
                    $tableGateway = $container->get(Model\PlaceTableGateway::class);
                    return new TableGateway\Place($tableGateway);
                },
                TableGateway\Recurent::class => function($container) {
                    $tableGateway = $container->get(Model\RecurentTableGateway::class);
                    return new TableGateway\Recurent($tableGateway);
                },
            ],
        ];
    }
}
