<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\AuthenticationService;

class AbstractController extends AbstractActionController
{
    protected $_container;
    protected $_user;

    public function __construct(ContainerInterface $container, $user = null)
    {
        $this->_container = $container;
        $this->_user      = $user;
    }

    public function getContainer()
    {
        return $this->_container;
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
