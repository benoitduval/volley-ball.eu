<?php

namespace Application\Model;

class Stats extends AbstractModel
{
    const POINT_US          = 1;
    const POINT_THEM        = 2;

    const POINT_SERVE       = 1;
    const POINT_ATTACK      = 2;
    const POINT_BLOCK       = 3;
    const FAULT_SERVE       = 4;
    const FAULT_ATTACK      = 5;
    const FAULT_DEFENCE     = 6;

    const DURING_BLOCK      = 7;
    const DURING_DEFENCE    = 8;

    protected $_id          = null;
    protected $_eventId     = null;
    protected $_scoreUs     = null;
    protected $_scoreThem   = null;
    protected $_pointFor    = null;
    protected $_duringPoint = null;
    protected $_set         = null;
}