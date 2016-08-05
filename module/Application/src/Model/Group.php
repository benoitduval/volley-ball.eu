<?php

namespace Application\Model;

class Group extends AbstractModel
{
    const RESPONSE_OK = 1;
    const RESPONSE_NO = 2;

    protected $_id          = null;
    protected $_userId      = null;
    protected $_userIds     = null;
    protected $_adminIds    = null;
    protected $_name        = null;
    protected $_weather     = null;
    protected $_ffvbUrl     = null;
    protected $_showUsers   = null;
    protected $_brand       = null;
    protected $_enable      = null;
    protected $_info        = null;
    protected $_description = null;

    public function toArray()
    {
        return array(
            'id'          => (int) $this->_id,
            'userId'      => (int) $this->_userId,
            'userIds'     => $this->_userIds,
            'adminIds'    => $this->_adminIds,
            'name'        => $this->_name,
            'weather'     => $this->_weather,
            'ffvbUrl'     => $this->_ffvbUrl,
            'showUsers'   => $this->_showUsers,
            'brand'       => $this->_brand,
            'enable'      => $this->_enable,
            'info'        => $this->_info,
            'description' => $this->_description,
        );
    }

    public function isAdmin($userId)
    {
        $adminIds = json_decode($this->_adminIds, true);
        if (is_null($adminIds)) $adminIds = [];
        return ($this->_userId == $userId || in_array($userId, $adminIds));
    }

    public function isMember($userId)
    {
        $userIds = json_decode($this->_userIds, true);
        if (is_null($userIds)) $userIds = [];
        return ($this->_userId == $userId || in_array($userId, $userIds));
    }

    public function initBrand()
    {
        $str = strtolower($this->name);
        $str = preg_replace('/ /', '-', $str);
        $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        $this->brand = $str;
    }
}