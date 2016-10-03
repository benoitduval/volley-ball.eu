<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class AbstractController extends AbstractActionController
{
    protected $_container;
    protected $_user;

    public function __construct(ContainerInterface $container, $user = false)
    {
        $this->_container = $container;
        $this->_user      = $user;
    }

    public function get($name)
    {
        return $this->_container->get($name);
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setActiveUser($user)
    {
        $this->_user = $user;
    }
}
