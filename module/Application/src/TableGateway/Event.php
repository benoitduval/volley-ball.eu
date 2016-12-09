<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;

class Event extends AbstractTableGateway
{
    public function getAllByGroupId($groupId)
    {
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $objs = $guestTable->fetchAll([
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

    public function getAllByUserId($userId)
    {
        $objs = $this->getContainer()->get(TableGateway\Guest::class)->fetchAll([
            'userId' => $userId
        ]);

        $events = [];
        $today  = new \DateTime('today midnight');
        if ($objs->toArray()) {
            $ids = [];
            foreach ($objs as $obj) {
                $ids[] = $obj->eventId;
            }
            $events = $this->fetchAll(['id' => $ids]);
        }
        return $events;
    }

    public function getActiveByUserId($userId)
    {
        $objs = $this->getContainer()->get(TableGateway\Guest::class)->fetchAll([
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
            ]);
        }
        return $events;
    }
}