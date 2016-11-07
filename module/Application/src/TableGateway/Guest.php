<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\Model;
use Application\TableGateway;

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

    public function getUserResponses($eventId)
    {
        $result = [
            'counters' => [
                Model\Guest::RESP_OK => 0,
                Model\Guest::RESP_NO => 0,
                Model\Guest::RESP_INCERTAIN => 0,
                Model\Guest::RESP_NO_ANSWER => 0,
            ],
            'users' => [
                Model\Guest::RESP_OK => [],
                Model\Guest::RESP_NO => [],
                Model\Guest::RESP_INCERTAIN => [],
                Model\Guest::RESP_NO_ANSWER => [],
            ]
        ];

        $guests = $this->fetchAll([
            'eventId' => $eventId
        ]);

        $userTable = $this->getContainer()->get(TableGateway\User::class);
        foreach ($guests as $guest) {
            $result['counters'][$guest->response]++;
             $user = $userTable->find($guest->userId);
             $result['users'][$guest->response][] = $user->getFullName();
        }

        return $result;
    }
}