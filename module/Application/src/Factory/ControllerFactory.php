<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Application\Service\AuthenticationService;

class ControllerFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = null;
        $authService = $container->get(AuthenticationService::class);
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
        }

        return new $requestedName($container, $user);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }
}