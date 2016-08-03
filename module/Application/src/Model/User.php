<?php
namespace Application\Model;

class User extends AbstractModel
{

    const DISPLAY_LARGE     = 1;
    const DISPLAY_SMALL     = 2;

    protected $_id          = null;
    protected $_firstname   = null;
    protected $_lastname    = null;
    protected $_email       = null;
    protected $_password    = null;
    protected $_status      = null;
    protected $_display     = null;

    public function getFullname()
    {
        return $this->_firstname . ' ' . $this->lastname;
    }
}

