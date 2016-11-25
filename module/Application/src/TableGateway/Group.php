<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model;

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

    public function getAdmins($groupId)
    {
        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $objs = $userGroupTable->fetchAll(['groupId' => $groupId, 'admin' => Model\UserGroup::ADMIN]);
        $users = [];

        $userTable = $this->getContainer()->get(TableGateway\User::class);
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->userId;
            }
            $users = $userTable->fetchAll([
                'id' => $ids
            ]);
        }
        return $users;
    }
}