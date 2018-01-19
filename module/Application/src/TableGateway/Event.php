<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;

class Event extends AbstractTableGateway
{
    public function getAllByGroupId($groupId)
    {
        $disponibilityTable = $this->getContainer()->get(TableGateway\Disponibility::class);
        $objs = $disponibilityTable->fetchAll([
            'groupId' => $groupId
        ]);

        $events = [];
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->eventId;
            }

            $events = $this->fetchAll([
                'id' => $ids
            ]);
        }
        return $events;
    }

    public function getAllByUserId($userId, $start, $end)
    {
        $objs = $this->getContainer()->get(TableGateway\Disponibility::class)->fetchAll([
            'userId' => $userId
        ]);

        $events = [];
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->eventId;
            }
            $events = $this->fetchAll([
                'id' => $ids,
                'date >= ?' => $start,
                'date <= ?' => $end,
            ]);
        }
        return $events;
    }

    public function getActiveByUserId($userId)
    {
        $objs = $this->getContainer()->get(TableGateway\Disponibility::class)->fetchAll([
            'userId' => $userId
        ]);

        $events = [];
        $today = new \DateTime('today midnight');
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->eventId;
            }
            $events = $this->fetchAll([
                'id' => $ids,
                'date >= ?' => $today->format('Y-m-d H:i:s')
            ], 'date ASC');
        }
        return $events;
    }

    public function getEventsByGroupId($groupId, $start, $end)
    {
        $result= [];
        $events = $this->fetchAll([
            'groupId' => $groupId,
            'date >= ?' => $start,
            'date <= ?' => $end,
        ]);
        foreach ($events as $event) {
            $result[$event->id] = $event;
        }
        return $result;
    }
}