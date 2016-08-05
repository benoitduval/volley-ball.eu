<?php

namespace Application\Model;

class Guest extends AbstractModel
{
    const RESP_NO_ANSWER = 0;
    const RESP_OK        = 1;
    const RESP_NO        = 2;
    const RESP_INCERTAIN = 3;

    protected $_id       = null;
    protected $_userId   = null;
    protected $_eventId  = null;
    protected $_response = null;
    protected $_date    = null;
    protected $_groupId = null;

    public function toArray()
    {
        return array(
            'id'       => (int) $this->_id,
            'userId'   => (int) $this->_userId,
            'eventId'  => (int) $this->_eventId,
            'response' => (int) $this->_response,
            'groupId'  => (int) $this->_groupId,
            'date'     => $this->_date,
        );
    }
}