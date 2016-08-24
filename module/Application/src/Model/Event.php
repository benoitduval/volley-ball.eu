<?php

namespace Application\Model;

class Event extends AbstractModel
{
    protected $_id       = null;
    protected $_userId   = null;
    protected $_placeId  = null;
    protected $_name     = null;
    protected $_comment  = null;
    protected $_date     = null;
    protected $_groupId  = null;

    public function toArray()
    {
        return array(
            'id'       => (int) $this->_id,
            'userId'   => (int) $this->_userId,
            'placeId'  => (int) $this->_placeId,
            'groupId'  => (int) $this->_groupId,
            'name'     => $this->_name,
            'comment'  => $this->_comment,
            'date'     => $this->_date,
        );
    }
}