<?php

namespace Application\Model;

class Place extends AbstractModel
{
    protected $_id      = null;
    protected $_name    = null;
    protected $_address = null;
    protected $_city    = null;
    protected $_zipCode = null;
    protected $_lat     = null;
    protected $_long    = null;
    protected $_groupId = null;

    public function getFullAddress()
    {
        return $this->address . ', ' . $this->zipCode . ' ' . $this->city;
    }
}