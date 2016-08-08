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
                TableGateway\Album::class => function($container) {
                    $tableGateway = $container->get(Model\AlbumTableGateway::class);
                    return new TableGateway\Album($tableGateway);
                },
                TableGateway\User::class => function($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new TableGateway\User($tableGateway);
                },
                TableGateway\Group::class => function($container) {
                    $tableGateway = $container->get(Model\GroupTableGateway::class);
                    return new TableGateway\Group($tableGateway);
                },
            ],
        ];
    }
}
