<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Application\TableGateway;
use Application\Model\Stats as Statistics;

class Stats extends AbstractTableGateway
{

    protected $_attackUs = [
        Statistics::POINT_ATTACK,
        Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LINE,
        Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::SMALL_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LARGE_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BLOCK_OUT,
        Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BIDOUILLE,
        Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LINE,
        Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::SMALL_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LARGE_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BLOCK_OUT,
        Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BIDOUILLE,
        Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::FIX,
        Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::DECA,
        Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::BEHIND,
        Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::BIDOUILLE,
        Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::SET_ATTACK,
        Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LINE,
        Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::SMALL_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LARGE_DIAG,
        Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BLOCK_OUT,
        Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BIDOUILLE,
    ];

    protected $_faultUs = [
        Statistics::FAULT_ATTACK,
        Statistics::FAULT_ATTACK . Statistics::POST_4,
        Statistics::FAULT_ATTACK . Statistics::POST_2,
        Statistics::FAULT_ATTACK . Statistics::POST_FIX,
        Statistics::FAULT_ATTACK . Statistics::POST_SETTER,
        Statistics::FAULT_ATTACK . Statistics::POST_3M,
    ];

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

    public function getEfficiencyStats($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getEfficiencyStats($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getEfficiencyStats($eventId, $i);
            }
        }
        return $result;
    }

    public function getZoneRepartitionStats($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getZoneRepartitionStats($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getZoneRepartitionStats($eventId, $i);
            }
        }
        return $result;
    }

    public function getDefenceStats($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getDefenceStats($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getDefenceStats($eventId, $i);
            }
        }
        return $result;
    }

    public function getFaultStats($eventId, $set = null)
    {
        $result = [];
        if ($set) {
            $result[$set] = $this->_getFaultStats($eventId, $set);
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $result[$i] = $this->_getFaultStats($eventId, $i);
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
            $data['us'][]   = ($stat->pointFor == Statistics::POINT_US) ? $stat->scoreUs: '-';
            $data['them'][] = ($stat->pointFor == Statistics::POINT_THEM) ? $stat->scoreThem: '-';
            switch ($stat->reason) {
                case Statistics::FAULT_DEFENCE:
                    $data['reason'][] = 'fas fa-shield-alt text-danger';
                    break;
                case Statistics::POINT_BLOCK:
                    $data['reason'][] = 'fas fa-ban text-success';
                    break;
                case Statistics::FAULT_ATTACK:
                case Statistics::FAULT_ATTACK . Statistics::POST_4:
                case Statistics::FAULT_ATTACK . Statistics::POST_2:
                case Statistics::FAULT_ATTACK . Statistics::POST_FIX:
                case Statistics::FAULT_ATTACK . Statistics::POST_SETTER:
                case Statistics::FAULT_ATTACK . Statistics::POST_3M:
                    $data['reason'][] = 'fa fa-crosshairs text-danger';
                    break;
                case Statistics::POINT_ATTACK:
                case Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LINE:
                case Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::SMALL_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LARGE_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BLOCK_OUT:
                case Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BIDOUILLE:
                case Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LINE:
                case Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::SMALL_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LARGE_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BLOCK_OUT:
                case Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BIDOUILLE:
                case Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::FIX:
                case Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::DECA:
                case Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::BEHIND:
                case Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::BIDOUILLE:
                case Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::SET_ATTACK:
                case Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LINE:
                case Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::SMALL_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LARGE_DIAG:
                case Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BLOCK_OUT:
                case Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BIDOUILLE:
                    $data['reason'][] = 'fa fa-crosshairs text-success';
                    break;
                case Statistics::POINT_SERVE:
                    $data['reason'][] = 'far fa-hand-paper text-success';
                    break;
                case Statistics::FAULT_SERVE:
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
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => [
                Statistics::FAULT_ATTACK,
                Statistics::FAULT_ATTACK . Statistics::POST_4,
                Statistics::FAULT_ATTACK . Statistics::POST_2,
                Statistics::FAULT_ATTACK . Statistics::POST_FIX,
                Statistics::FAULT_ATTACK . Statistics::POST_SETTER,
                Statistics::FAULT_ATTACK . Statistics::POST_3M,
            ]
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_US,
            'reason' => $this->_attackUs,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::POINT_SERVE,
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
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_SERVE,
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
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => $this->_faultUs
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_US,
            'reason' => $this->_attackUs,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::POINT_SERVE,
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
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_DEFENCE,
        ]);

        $blockPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_BLOCK,
        ]);

        $attackFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_ATTACK,
        ]);

        $attackPoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_ATTACK,
        ]);

        $serveFault = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_US,
            'reason' => Statistics::FAULT_SERVE,
        ]);

        $servePoint = $this->count([
            'eventId' => $eventId,
            'set' => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason' => Statistics::POINT_SERVE,
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

    private function _getEfficiencyStats($eventId, $set)
    {
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return [];

        $attackFault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => $this->_faultUs
        ]);

        $attackPoint = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => $this->_attackUs,
        ]);

        $blockThem = $this->sum('blockThem', [
            'eventId'  => $eventId,
            'set'      => $set,
            'blockThem > ?' => 0,
        ]);

        $defenceThem = $this->sum('defenceThem', [
            'eventId'  => $eventId,
            'set'      => $set,
            'defenceThem > ?' => 0,
        ]);

        $result = [
            'fault' => $attackFault,
            'point' => $attackPoint,
            'block' => $blockThem,
            'defence' => $defenceThem,
        ];

        return $result;
    }

    private function _getFaultStats($eventId, $set)
    {
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return [];

        $post4Fault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => Statistics::FAULT_ATTACK . Statistics::POST_4,
        ]);

        $post2Fault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => Statistics::FAULT_ATTACK . Statistics::POST_2,
        ]);

        $postCenterFault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => Statistics::FAULT_ATTACK . Statistics::POST_FIX,
        ]);

        $postSetFault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => Statistics::FAULT_ATTACK . Statistics::POST_SETTER,
        ]);

        $post3mFault = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_THEM,
            'reason'   => Statistics::FAULT_ATTACK . Statistics::POST_3M,
        ]);

        $result = [
            '4' => $post4Fault,
            '2' => $post2Fault,
            'center' => $postCenterFault,
            '3m' => $post3mFault,
            'setter' => $postSetFault,
        ];

        return $result;
    }


    private function _getZoneRepartitionStats($eventId, $set)
    {
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return [];

        $line4 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LINE,
        ]);

        $line2 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LINE,
        ]);

        $line3m = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LINE,
        ]);

        $bidouille4 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BIDOUILLE,
        ]);

        $bidouille2 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BIDOUILLE,
        ]);

        $bidouille3m = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BIDOUILLE,
        ]);

        $blockOut4 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::BLOCK_OUT,
        ]);

        $blockOut2 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::BLOCK_OUT,
        ]);

        $blockOut3m = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::BLOCK_OUT,
        ]);

        $largeDiag4 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::LARGE_DIAG,
        ]);

        $largeDiag2 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::LARGE_DIAG,
        ]);

        $largeDiag3m = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::LARGE_DIAG,
        ]);

        $smallDiag4 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_4 . Statistics::SMALL_DIAG,
        ]);

        $smallDiag2 = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_2 . Statistics::SMALL_DIAG,
        ]);

        $smallDiag3m = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_3M . Statistics::SMALL_DIAG,
        ]);

        $fixCenter = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::FIX,
        ]);

        $decaCenter = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::DECA,
        ]);

        $behindCenter = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_FIX . Statistics::BEHIND,
        ]);

        $setAttack = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::SET_ATTACK,
        ]);

        $setBidouille = $this->count([
            'eventId'  => $eventId,
            'set'      => $set,
            'pointFor' => Statistics::POINT_US,
            'reason'   => Statistics::POINT_ATTACK . Statistics::POST_SETTER . Statistics::BIDOUILLE,
        ]);

        $line = $line2 + $line4 + $line3m;
        $largeDiag = $largeDiag3m + $largeDiag2 + $largeDiag4;
        $smallDiag = $smallDiag3m + $smallDiag2 + $smallDiag4;
        $blockOut = $blockOut3m + $blockOut2 + $blockOut4;
        $bidouille = $bidouille3m + $bidouille2 + $bidouille4 + $setBidouille;

        $set = $setAttack + $setBidouille;
        $center = $decaCenter + $fixCenter + $behindCenter;
        $post4 = $largeDiag4 + $smallDiag4 + $line4 + $bidouille4 + $blockOut4;
        $post2 = $largeDiag2 + $smallDiag2 + $line2 + $bidouille2 + $blockOut2;
        $post3m = $largeDiag3m + $smallDiag3m + $line3m + $bidouille3m + $blockOut3m;

        $result = [
            'line'         => $line,
            'line4'        => $line4,
            'line3m'       => $line3m,
            'line2'        => $line2,
            'largeDiag'    => $largeDiag,
            'largeDiag2'   => $largeDiag2,
            'largeDiag4'   => $largeDiag4,
            'largeDiag3m'  => $largeDiag3m,
            'smallDiag'    => $smallDiag,
            'smallDiag2'   => $smallDiag2,
            'smallDiag4'   => $smallDiag4,
            'smallDiag3m'  => $smallDiag3m,
            'bidouille'    => $bidouille,
            'bidouille2'   => $bidouille2,
            'bidouille4'   => $bidouille4,
            'bidouille3m'  => $bidouille3m,
            'blockOut'     => $blockOut,
            'blockOut2'    => $blockOut2,
            'blockOut4'    => $blockOut4,
            'blockOut3m'   => $blockOut3m,
            'center'       => $center,
            'decaCenter'   => $decaCenter,
            'fixCenter'    => $fixCenter,
            'behindCenter' => $behindCenter,
            'set'          => $set,
            'setAttack'    => $setAttack,
            'setBidouille' => $setBidouille,
            'post4'        => $post4,
            'post2'        => $post2,
            'post3m'       => $post3m,
        ];

        return $result;
    }

    private function _getDefenceStats($eventId, $set)
    {
        if (!$this->fetchOne(['eventId' => $eventId, 'set' => $set])) return [];

        $blockThem = $this->sum('blockThem', [
            'eventId'  => $eventId,
            'set'      => $set,
            'blockThem > ?' => 0,
        ]);

        $blockUs = $this->sum('blockUs', [
            'eventId'  => $eventId,
            'set'      => $set,
            'blockUs > ?' => 0,
        ]);

        $defenceThem = $this->sum('defenceThem', [
            'eventId'  => $eventId,
            'set'      => $set,
            'defenceThem > ?' => 0,
        ]);

        $defenceUs = $this->sum('defenceUs', [
            'eventId'  => $eventId,
            'set'      => $set,
            'defenceUs > ?' => 0,
        ]);

        $result = [
            'blockUs'   => $blockUs,
            'blockThem' => $blockThem,
            'defenceUs' => $defenceUs,
            'defenceThem' => $defenceThem,
        ];

        return $result;
    }
}