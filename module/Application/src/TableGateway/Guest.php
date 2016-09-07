<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\Model;

class Guest extends AbstractTableGateway
{

    public function getCounters($eventId)
    {
        $result = [
            Model\Guest::RESP_OK => 0,
            Model\Guest::RESP_NO => 0,
            Model\Guest::RESP_INCERTAIN => 0,
            Model\Guest::RESP_NO_ANSWER => 0,
        ];
        $guests = $this->fetchAll([
            'eventId' => $eventId
        ]);

        foreach ($guests as $guest) {
            $result[$guest->response]++;
        }

        return $result;
    }
}