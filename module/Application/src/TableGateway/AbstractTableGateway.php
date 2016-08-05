<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class AbstractTableGateway
{

    private $_tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    public function getTableGateway()
    {
        return $this->_tableGateway;
    }

    public function fetchAll($where = [], $orderBy = 'id DESC')
    {
        return $this->getTableGateway()->select(function($select) use ($orderBy, $where) {
            $select->where($where)->order($orderBy);
        });
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

    public function save($model)
    {
        $data = $model->toArray();
        $id   = (int) $model->id;

        if ($id === 0) {
            $this->getTableGateway()->insert($data);
            return;
        }

        if (!$this->find($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->getTableGateway()->update($data, ['id' => $id]);
    }

    public function delete($id)
    {
        $this->getTableGateway()->delete(['id' => (int) $id]);
    }

}