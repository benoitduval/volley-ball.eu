<?php

namespace Application\Model;

use Application\Service\Date as Date;

class Comment extends AbstractModel
{
    protected $_id      = null;
    protected $_userId  = null;
    protected $_eventId = null;
    protected $_comment = null;
    protected $_date    = null;

    public function toArray()
    {
        return array(
            'id'      => (int) $this->_id,
            'userId'  => (int) $this->_userId,
            'eventId' => (int) $this->_eventId,
            'comment' => $this->_comment,
            'date'    => $this->_date,
        );
    }
}
