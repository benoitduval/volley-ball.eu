<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Application\Service\AuthenticationService;
use Application\TableGateway;

class ControllerFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = null;
        $authService = $container->get(AuthenticationService::class);
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
        }

        $tables = [
            'group'     => $container->get(TableGateway\Group::class),
            'userGroup' => $container->get(TableGateway\UserGroup::class),
            'join'      => $container->get(TableGateway\Join::class),
            'user'      => $container->get(TableGateway\User::class),
            'event'     => $container->get(TableGateway\Event::class),
            'recurent'  => $container->get(TableGateway\Recurent::class),
            'match'     => $container->get(TableGateway\Match::class),
            'guest'     => $container->get(TableGateway\Guest::class),
        ];

        return new $requestedName($container, $tables, $user);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }
}