<?php

namespace Application\Model;

class Stats extends AbstractModel
{
    const POINT_US            = 1;
    const POINT_THEM          = 2;

    const POINT_SERVE         = 1;
    const POINT_ATTACK        = 2;
    const POINT_BLOCK         = 3;
    const FAULT_SERVE         = 4;
    const FAULT_ATTACK        = 5;
    const FAULT_DEFENCE       = 6;

    const DURING_BLOCK        = 7;
    const DURING_DEFENCE      = 8;

    const POST_2              = 1;
    const POST_FIX            = 2;
    const POST_3M             = 3;
    const POST_SETTER         = 4;
    const POST_4              = 5;

    const SMALL_DIAG          = 1;
    const LARGE_DIAG          = 2;
    const BLOCK_OUT           = 3;
    const BIDOUILLE           = 4;
    const LINE                = 5;

    protected $_id            = null;
    protected $_eventId       = null;
    protected $_scoreUs       = null;
    protected $_scoreThem     = null;
    protected $_pointFor      = null;
    protected $_set           = null;
    protected $_reason        = null;
    protected $_blockUs       = null;
    protected $_blockThem     = null;
    protected $_defenceUs     = null;
    protected $_defenceThem   = null;
}