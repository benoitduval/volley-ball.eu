<?php

namespace Application\Model;

class Badge extends AbstractModel
{

    const TYPE_EVENT   = 1;
    const TYPE_COMMENT = 2;
    const TYPE_GROUP   = 3;

    protected $_id          = null;
    protected $_userId      = null;
    protected $_itemType    = null;
    protected $_itemId      = null;

    public function toArray()
    {
        return array(
            'id'       => (int) $this->_id,
            'userId'   => (int) $this->_userId,
            'itemType' => (int) $this->_itemType,
            'itemId'   => (int) $this->_itemId,
        );
    }
}
