<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model;

class Stats extends AbstractTableGateway
{
    public function getSetsStats($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getStats($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getStats($eventId, $i);
            }
        }
        return $result;
    }

    public function setsLastScore($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_setsLastScore($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_setsLastScore($eventId, $i);
            }
        }
        return $result;
    }


    public function getSetsHistory($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getSetsHistory($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getSetsHistory($eventId, $i);
            }
        }
        return $result;
    }

    private function _setsLastScore($eventId, $set)
    {
        $score = [];
        if (!($stat = $this->fetchOne(['eventId' => $eventId, 'set' => $set]))) return $score;
        return [$stat->scoreUs, $stat->scoreThem];
    }

    private function _getSetsHistory($eventId, $set)
    {
        $result = [];
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return $result;
        $stats = $this->fetchAll(['eventId' => $eventId, 'set' => $set], 'id ASC');
        $data  = [];
        foreach ($stats as $stat) {
            $data['us'][]   = ($stat->pointFor == Model\Stats::POINT_US) ? $stat->scoreUs: '-';
            $data['them'][] = ($stat->pointFor == Model\Stats::POINT_THEM) ? $stat->scoreThem: '-';
            switch ($stat->reason) {
                case Model\Stats::FAULT_DEFENCE:
                    $data['reason'][] = 'fa-shield text-danger';
                    break;
                case Model\Stats::POINT_BLOCK:
                    $data['reason'][] = 'fa-ban text-success';
                    break;
                case Model\Stats::FAULT_ATTACK:
                    $data['reason'][] = 'fa-crosshairs text-danger';
                    break;
                case Model\Stats::POINT_ATTACK:
                    $data['reason'][] = 'fa-crosshairs text-success';
                    break;
                case Model\Stats::POINT_SERVE:
                    $data['reason'][] = 'fa-hand-paper-o text-success';
                    break;
                case Model\Stats::FAULT_SERVE:
                    $data['reason'][] = 'fa-hand-paper-o text-danger';
                    break;
            }
        }
        return $data;
    }

    public function getOverallStats($eventId)
    {
        if (!$this->fetchOne(['eventId' => $eventId])) return [];

        $defenceFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_SERVE,
        ]);

        $totalFaults = $defenceFault + $attackFault + $serveFault;

        $result['us'] = json_encode([
            $servePoint,
            $attackPoint,
            $blockPoint,
            $serveFault,
            $attackFault,
            $defenceFault,
            $totalFaults,
        ]);

        $defenceFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_SERVE,
        ]);

        $totalFaults = $defenceFault + $attackFault + $serveFault;

        $result['them'] = json_encode([
            $servePoint,
            $attackPoint,
            $blockPoint,
            $serveFault,
            $attackFault,
            $defenceFault,
            $totalFaults,
        ]);

        return $result;
    }

    private function _getStats($eventId, $set)
    {
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return [];

        $defenceFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::POINT_SERVE,
        ]);

        $totalFaults = $defenceFault + $attackFault + $serveFault;

        $result['us'] = json_encode([
            $servePoint,
            $attackPoint,
            $blockPoint,
            $serveFault,
            $attackFault,
            $defenceFault,
            $totalFaults,
        ]);

        $defenceFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => Model\Stats::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_THEM,
            'reason' => Model\Stats::POINT_SERVE,
        ]);

        $totalFaults = $defenceFault + $attackFault + $serveFault;

        $result['them'] = json_encode([
            $servePoint,
            $attackPoint,
            $blockPoint,
            $serveFault,
            $attackFault,
            $defenceFault,
            $totalFaults,
        ]);

        return $result;
    }
}