<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;

class Event extends AbstractTableGateway
{
    public function getUserEvents($userId)
    {
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $objs = $guestTable->fetchAll([
            'userId' => $userId
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

    public function getGroupEvents($groupId)
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
}