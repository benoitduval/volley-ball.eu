<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model;

class Stats extends AbstractTableGateway
{
    public function getSetsHistory($eventId)
    {
        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!$this->fetchOne(['eventId' => $eventId, 'set' => $i])) break;
            $data = $this->fetchAll(['eventId' => $eventId, 'set' => $i], 'id ASC');
            $set  = [];
            foreach ($data as $stat) {
                $set['us'][]   = ($stat->pointFor == Model\Stats::POINT_US) ? $stat->scoreUs: '-';
                $set['them'][] = ($stat->pointFor == Model\Stats::POINT_THEM) ? $stat->scoreThem: '-';
            }
            $result[$i] = $set;
        }
        return $result;
    }

    public function setsLastScore($eventId)
    {
        $score = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!($stat = $this->fetchOne(['eventId' => $eventId, 'set' => $i]))) break;
            $score[$i] = $stat->scoreUs . ' - ' . $stat->scoreThem;
        }
        return $score;
    }

    public function getSetsStats($eventId)
    {
        $result = [];
        $data   = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!$this->fetchOne(['eventId' => $eventId, 'set' => $i])) break;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::FAULT_DEFENCE,
            ]);
            $defenceFault = $count * -1;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_US,
                'reason' => Model\Stats::POINT_BLOCK,
            ]);
            $blockPoint = $count * -1;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::FAULT_ATTACK,
            ]);
            $attackFault = $count * -1;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_US,
                'reason' => Model\Stats::POINT_ATTACK,
            ]);
            $attackPoint = $count * -1;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::FAULT_SERVE,
            ]);
            $serveFault = $count * -1;

            $count = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_US,
                'reason' => Model\Stats::POINT_SERVE,
            ]);
            $servePoint = $count * -1;

            $totalFaults = $defenceFault + $attackFault + $serveFault;

            $result[$i]['us'] = json_encode([
                $totalFaults,
                $defenceFault,
                $blockPoint,
                $attackFault,
                $attackPoint,
                $serveFault,
                $servePoint
            ]);

            $defence = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'defenceThem' => 1,
            ]);

            $blockPoint = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::POINT_BLOCK,
            ]);

            $attackFault = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_US,
                'reason' => Model\Stats::FAULT_ATTACK,
            ]);

            $attackPoint = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::POINT_ATTACK,
            ]);

            $serveFault = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_US,
                'reason' => Model\Stats::FAULT_SERVE,
            ]);

            $servePoint = $this->count([
                'eventId' => $eventId,
                'set' => $i,
                'pointFor' => Model\Stats::POINT_THEM,
                'reason' => Model\Stats::POINT_SERVE,
            ]);

            $totalFaults = $defenceFault + $attackFault + $serveFault;

            $result[$i]['them'] = json_encode([
                $totalFaults,
                $defenceFault,
                $blockPoint,
                $attackFault,
                $attackPoint,
                $serveFault,
                $servePoint
            ]);

        }
        return $result;
    }
}