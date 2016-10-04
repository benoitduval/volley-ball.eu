<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class TableGatewayFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        preg_match('/([a-zA-Z]*)TableGateway/', $requestedName, $matches);
        list(,$name) = $matches;

        $dbAdapter          = $container->get(AdapterInterface::class);
        $resultSetPrototype = new ResultSet();
        $model = '\\Application\\Model\\' . $name;
        $resultSetPrototype->setArrayObjectPrototype(new $model($container));

        return new TableGateway(lcfirst($name), $dbAdapter, null, $resultSetPrototype);
    }
}