<?php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractEntityFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var CommonTableGateway $requestedName
         * @var CommonEntity $entity
         */
        // $entityName = str_replace('Mapper', 'Entity', $requestedName);
        // $entity = new $entityName();
        // $mapper = new $requestedName($container->get(AdapterInterface::class), $entity);
        // return $mapper;
        return [];
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
    	\Zend\Debug\Debug::dump($requestedName);
    	return false;
    	\Zend\Debug\Debug::dump($requestedName);die;
    	\Zend\Debug\Debug::dump(class_exists(\Model\Album::class));die;
        $entityName     = str_replace('Mapper', 'Entity', $requestedName);
        $isClassExists  = class_exists($requestedName);
        $isEntityExists = class_exists($entityName);
        $isMapper = preg_match('/^[a-z]+\\\Mapper\\\.*Mapper/i', $requestedName);
        return (
            $isClassExists && $isEntityExists && $isMapper
        );
    }
}