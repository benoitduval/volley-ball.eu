<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model;
use Application\Service\Date;

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

    public function getDisponibilities($groupId)
    {
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $memcached  = $this->getContainer()->get('memcached');
        $result['last'] = $result['current'] = [
            '09' => null,
            '10' => null,
            '11' => null,
            '12' => null,
            '01' => null,
            '02' => null,
            '03' => null,
            '04' => null,
            '05' => null,
            '06' => null,
            '07' => null,
            '08' => null,
        ];

        $eventByMonth = [];
        foreach (Date::getSeasonsDates() as $label => $dates) {

            $events = $eventTable->fetchAll([
                'groupId'  => $groupId,
                'date >= ?' => date('Y-m-d H:i:s', $dates['from']),
                'date <= ?' => date('Y-m-d H:i:s', $dates['to']),
            ], 'date ASC');

            foreach ($events as $event) {
                $eventDate = \Datetime::createFromFormat('Y-m-d H:i:s', $event->date);
                $year = $eventDate->format('Y');
                $month = $eventDate->format('m');
                if (!isset($eventByMonth[$year . '-' . $month])) $eventByMonth[$year . '-' . $month] = [];
                $eventByMonth[$year . '-' . $month][] = $event->id;
            }

            foreach ($eventByMonth as $date => $eventIds) {
                $count = $guestTable->count([
                    'eventId'  => $eventIds,
                    'response' => \Application\Model\Guest::RESP_OK,
                ]);
                if ($count) $count = $count / count($eventIds);
                $result[$label][$month] = $count;
            }
        }

        return $result;
    }

    public function getScoresBySeasons($groupId)
    {
        $scores['last'] = $scores['current'] = [
            '3 / 0' => 0,
            '3 / 1' => 0,
            '3 / 2' => 0,
            '0 / 3' => 0,
            '1 / 3' => 0,
            '2 / 3' => 0,
        ];
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $matchTable = $this->getContainer()->get(TableGateway\Match::class);
        foreach (Date::getSeasonsDates() as $label => $dates) {
            $events = $eventTable->fetchAll([
                'groupId'  => $groupId,
                'date > ?' => date('Y-m-d H:i:s', $dates['from']),
                'date < ?' => date('Y-m-d H:i:s', $dates['to']),
            ], 'date DESC');

            foreach ($events as $event) {
                if ($match = $matchTable->fetchOne(['eventId' => $event->id, 'sets is NOT NULL'])) {
                    $scores[$label][$match->sets] ++;
                }
            }
        }
        return $scores;
    }
}