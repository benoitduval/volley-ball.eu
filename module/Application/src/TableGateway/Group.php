<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;

class Group extends AbstractTableGateway
{
    public function getUserGroups($userId)
    {
        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $objs = $userGroupTable->fetchAll([
            'userId' => $userId
        ]);

        $groups = [];
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->groupId;
            }

            $groups = $this->fetchAll([
                'id' => $ids
            ]);
        }
        return $groups;
    }
}