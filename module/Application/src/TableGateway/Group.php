<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model;

class Group extends AbstractTableGateway
{
    public function getAllByUserId($userId)
    {
        $key = 'user.groups.' . $userId;
        $memcached = $this->getContainer()->get('memcached');
        if (!($groups = $memcached->getItem($key))) {
            $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
            $objs = $userGroupTable->fetchAll([
                'userId' => $userId
            ]);

            $result = [];
            if ($objs->toArray()) {
                $ids = [];
                foreach ($objs as $obj) {
                    $ids[] = $obj->groupId;
                }

                $result = $this->fetchAll([
                    'id' => $ids
                ]);
            }

            foreach ($result as $group) {
                $groups[$group->id] = $group;
            }
            $memcached->setItem($key, $groups);
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