<?php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
    	\Zend\Debug\Debug::dump([$requestedName, $options]);die;
        // return new $requestedName($apiKey,§ $url);
    }
}