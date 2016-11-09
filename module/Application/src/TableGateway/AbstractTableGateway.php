<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Interop\Container\ContainerInterface;

class AbstractTableGateway
{

    private $_tableGateway;
    private $_container;

    public function __construct(TableGatewayInterface $tableGateway, ContainerInterface $container)
    {
        $this->_tableGateway = $tableGateway;
        $this->_container = $container;
    }

    public function getContainer()
    {
        return $this->_container;
    }

    public function getTableGateway()
    {
        return $this->_tableGateway;
    }

    public function fetchAll($where = [], $orderBy = 'id DESC')
    {
        $result = $this->getTableGateway()->select(function($select) use ($orderBy, $where) {
            $select->where($where)->order($orderBy);
        });
        $result->buffer();
        return $result;

    }

    public function find($id)
    {
        $id = (int) $id;
        $rowset = $this->getTableGateway()->select(['id' => $id]);
        return $rowset->current();
    }

    public function fetchOne($where = [])
    {
        $rowset = $this->fetchAll($where);
        return $rowset->current();
    }

    public function save($data)
    {
        if (is_object($data)) $data = $data->toArray();

        foreach ($data as $key => $value) {
            $property = '_' . $key;
            $obj = $this->getTableGateway()->getResultSetPrototype()->getArrayObjectPrototype();
            if (!property_exists($obj, $property)) {
                unset($data[$key]);
            }
        }

        $id = isset($data['id']) ? $data['id'] : 0;

        if ($id === 0) {
            $this->getTableGateway()->insert($data);
            $id = $this->getTableGateway()->getLastInsertValue();
            return $this->find($id);
        }

        if (!$this->find($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->getTableGateway()->update($data, ['id' => $id]);
        return $this->find($id);
    }

    public function delete($params)
    {
        if (is_array($params)) {
            $this->getTableGateway()->delete($params);
        } else if (is_object($params)) {
            $this->getTableGateway()->delete(['id' => (int) $params->id]);
        }
    }

}