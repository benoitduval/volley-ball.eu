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
                    $data['reason'][] = 'fas fa-shield-alt text-danger';
                    break;
                case Model\Stats::POINT_BLOCK:
                    $data['reason'][] = 'fas fa-ban text-success';
                    break;
                case Model\Stats::FAULT_ATTACK:
                case Model\Stats::FAULT_ATTACK . Model\Stats::POST_4:
                case Model\Stats::FAULT_ATTACK . Model\Stats::POST_2:
                case Model\Stats::FAULT_ATTACK . Model\Stats::POST_FIX:
                case Model\Stats::FAULT_ATTACK . Model\Stats::POST_SETTER:
                case Model\Stats::FAULT_ATTACK . Model\Stats::POST_3M:
                    $data['reason'][] = 'fa fa-crosshairs text-danger';
                    break;
                case Model\Stats::POINT_ATTACK:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LINE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::SMALL_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LARGE_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BLOCK_OUT:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BIDOUILLE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LINE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::SMALL_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LARGE_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BLOCK_OUT:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BIDOUILLE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::FIX:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::DECA:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::BEHIND:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::BIDOUILLE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::SET_ATTACK:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LINE:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::SMALL_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LARGE_DIAG:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BLOCK_OUT:
                case Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BIDOUILLE:
                    $data['reason'][] = 'fa fa-crosshairs text-success';
                    break;
                case Model\Stats::POINT_SERVE:
                    $data['reason'][] = 'far fa-hand-paper text-success';
                    break;
                case Model\Stats::FAULT_SERVE:
                    $data['reason'][] = 'far fa-hand-paper text-danger';
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
            'reason' => [
                Model\Stats::FAULT_ATTACK,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_4,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_2,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_FIX,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_SETTER,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_3M,
            ]
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => [
                Model\Stats::POINT_ATTACK,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::FIX,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::DECA,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::BEHIND,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::SET_ATTACK,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BIDOUILLE,
            ],
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
            'reason' => [
                Model\Stats::FAULT_ATTACK,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_4,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_2,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_FIX,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_SETTER,
                Model\Stats::FAULT_ATTACK . Model\Stats::POST_3M,
            ]
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Model\Stats::POINT_US,
            'reason' => [
                Model\Stats::POINT_ATTACK,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_4 . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_2 . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::FIX,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::DECA,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_FIX . Model\Stats::BEHIND,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::BIDOUILLE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_SETTER . Model\Stats::SET_ATTACK,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LINE,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::SMALL_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::LARGE_DIAG,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BLOCK_OUT,
                Model\Stats::POINT_ATTACK . Model\Stats::POST_3M . Model\Stats::BIDOUILLE,

            ],
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