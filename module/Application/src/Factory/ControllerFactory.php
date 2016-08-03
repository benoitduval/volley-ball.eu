<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ControllerFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }
}