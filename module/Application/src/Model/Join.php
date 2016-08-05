<?php

namespace Application\Model;

class Join extends AbstractModel
{
    protected $_id          = null;
    protected $_userId      = null;
    protected $_groupId     = null;
    protected $_response    = null;

    const RESPONSE_WAITING = 1;
    const RESPONSE_REFUSED = 2;

    public function toArray()
    {
        return array(
            'id'        => (int) $this->_id,
            'userId'    => $this->_userId,
            'groupId'   => $this->_groupId,
            'response'  => $this->_response,
        );
    }
}
