<?php
namespace Application\Model;

use Application\Tablegateway;

class User extends AbstractModel
{

    const DISPLAY_SMALL     = 1;
    const DISPLAY_LARGE     = 2;
    const DISPLAY_TABLE     = 3;

    const HAS_TO_CONFIRM    = 1;
    const CONFIRMED         = 2;

    protected $_id          = null;
    protected $_firstname   = null;
    protected $_lastname    = null;
    protected $_email       = null;
    protected $_password    = null;
    protected $_status      = null;
    protected $_display     = null;
    protected $_phone       = null;
    protected $_licence     = null;

    public function getFullname()
    {
        return $this->_firstname . ' ' . $this->lastname;
    }

    public function getGroups()
    {
        var_dump($this->getContainer()->get(Tablegateway\UserGroup::class));
    }
}