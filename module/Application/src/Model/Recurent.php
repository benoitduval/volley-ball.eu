<?php
namespace Application\Model;

class Recurent extends AbstractModel
{
    protected $_id          = null;
    protected $_userId      = null;
    protected $_groupId     = null;
    protected $_day         = null;
    protected $_time        = null;
    protected $_sendDay     = null;
    protected $_status      = null;
    protected $_name        = null;
    protected $_placeId     = null;

    const ACTIVE   = 1;
    const INACTIVE = 2;

    public function toArray()
    {
        return array(
            'id'      => (int) $this->_id,
            'userId'  => $this->_userId,
            'groupId' => $this->_groupId,
            'status'  => $this->_status,
            'day'     => $this->_day,
            'time'    => $this->_time,
            'sendDay' => $this->_sendDay,
            'name'    => $this->_name,
            'placeId' => $this->_placeId,
        );
    }
}
