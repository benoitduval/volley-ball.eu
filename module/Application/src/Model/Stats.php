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

    const SERVE_POINT       = 10;
    const SERVE_EASY        = 11;
    const SERVE_HARD        = 12;
    const SERVE_ERROR       = 13;

    const RECEP_ACE         = 20;
    const RECEP_PLAY        = 21;
    const RECEP_EASY        = 22;

    const SET_ERROR         = 30;
    const SET_4             = 31;
    const SET_CENTER        = 32;
    const SET_2             = 33;
    const SET_3M            = 34;

    const ATTACK_ERROR      = 40;
    const ATTACK_PLAY       = 41;
    const ATTACK_POINT      = 42;

    const DEF_ERROR         = 50;
    const DEF_PLAY          = 51;
    const DEF_EASY          = 52;

    const BLOCK_OUT         = 60;
    const BLOCK_PLAY        = 61;
    const BLOCK_POINT       = 62;

    protected $_id          = null;
    protected $_eventId     = null;
    protected $_scoreUs     = null;
    protected $_scoreThem   = null;
    protected $_pointFor    = null;
    protected $_set         = null;
    protected $_reason      = null;
    protected $_blockUs     = null;
    protected $_blockThem   = null;
    protected $_defenceUs   = null;
    protected $_defenceThem = null;
}