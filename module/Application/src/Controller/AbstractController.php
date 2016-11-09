<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Application\TableGateway;


class AbstractController extends AbstractActionController
{
    protected $_container;
    protected $_user       = null;
    protected $_userGroups = [];

    public function __construct(ContainerInterface $container, $user = false)
    {
        $this->_container       = $container;
        $this->_user            = $user;
        $this->_userGroups      = $this->getUserGroups();
    }

    public function get($name)
    {
        return $this->_container->get($name);
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function getUserGroups()
    {
        $groups = [];
        if (empty($this->_userGroups) && $this->_user) {
            $groupTable     = $this->get(TableGateway\Group::class);
            foreach ($groupTable->getUserGroups($this->_user->id) as $group) {
                $groups[$group->id] = $group;
            }
            $this->_userGroups = $groups;
        }
        return $this->_userGroups;
    }

    public function setActiveUser($user)
    {
        $this->_user = $user;
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $config = $this->get('config');
        $this->layout()->vCss   = $config['version']['css'];
        $this->layout()->vJs    = $config['version']['js'];
        $this->layout()->user   = $this->getUser();
        $this->layout()->groups = $this->getUserGroups();
        return parent::onDispatch($e);
    }
}
