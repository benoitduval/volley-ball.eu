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

    public function getGroupDisponibility($groupId, $from, $to)
    {
        $from = date('Y', $from);
        $to = date('Y', $to);
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);

        $result = [];
        $memcached = $this->getContainer()->get('memcached');

        foreach (['09', '10', '11', '12'] as $month) {
            $key = 'disponibility.group.'. $groupId . '.date.' . $from . '.' . $month;
            if (!($count = $memcached->getItem($key))) {
                $events = $eventTable->fetchAll([
                    'groupId'  => $groupId,
                    'date > ?' => $from . '-' . $month . '-01 00:00:00',
                    'date < ?' => $from . '-' . $month . '-31 23:59:59',
                ]);

                $count = 0;
                foreach ($events as $event) {
                    $count += $guestTable->count([
                        'eventId'  => $event->id,
                        'response' => \Application\Model\Guest::RESP_OK,
                    ]);
                }
                if ($count) {
                    $count = floor($count / count($events));
                }
                $memcached->setItem($key, $count);
            }
            $result[] = $count;
        }

        foreach (['01', '02', '03', '04', '05', '06', '07', '08'] as $month) {
            $key = 'disponibility.group.'. $groupId . '.date.' . $to . '.' . $month;
            if (!($count = $memcached->getItem($key))) {
                $events = $eventTable->fetchAll([
                    'groupId'  => $groupId,
                    'date > ?' => $to . '-' . $month . '-01 00:00:00',
                    'date < ?' => $to . '-' . $month . '-31 23:59:59',
                ]);

                $count = 0;
                foreach ($events as $event) {
                    $count += $guestTable->count([
                        'eventId'  => $event->id,
                        'response' => \Application\Model\Guest::RESP_OK,
                    ]);
                }
                if ($count) $count = floor($count / count($events));
                $memcached->setItem($key, $count);
            }
            $result[] = $count;
        }

        return json_encode($result);
    }

}