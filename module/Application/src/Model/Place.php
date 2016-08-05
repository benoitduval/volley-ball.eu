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

    public function toArray()
    {
        return array(
            'id'      => (int) $this->_id,
            'name'    => $this->_name,
            'address' => $this->_address,
            'city'    => $this->_city,
            'zipCode' => (int) $this->_zipCode,
            'lat'     => $this->_lat,
            'long'    => $this->_long,
            'groupId' => $this->_groupId,
        );
    }
}