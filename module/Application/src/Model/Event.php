<?php

namespace Application\Model;

class Event extends AbstractModel
{
    protected $_id       = null;
    protected $_groupId  = null;
    protected $_name     = null;
    protected $_comment  = null;
    protected $_date     = null;
    protected $_place    = null;
    protected $_address  = null;
    protected $_city     = null;
    protected $_zipCode  = null;
    protected $_lat      = null;
    protected $_long     = null;

    public function getFullAddress()
    {
        return $this->address . ', ' . $this->zipCode . ' ' . $this->city;
    }
}