<?php

namespace Application\Service;

use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Authentication\Storage\Session as SessionStorage;
use Interop\Container\ContainerInterface;
use \Application\TableGateway;

class AuthenticationService extends \Zend\Authentication\AuthenticationService
{
    protected $_container;

    public function __construct(ContainerInterface $container, Adapter $dbAdapter)
    {
        $this->_container = $container;
        $storage = new CallbackCheckAdapter($dbAdapter, 'user', 'email', 'password');
        // $storage = new CallbackCheckAdapter($dbAdapter, 'user', 'email', 'password', array($this, 'callBack'));

        $select = $storage->getDbSelect();
        // $select->where('status = ?', User::ACTIVE);
        parent::__construct(new SessionStorage(), $storage);
    }

    public function getIdentity()
    {
        $identity = parent::getIdentity();

        $userTable = $this->_container->get(TableGateway\User::class);
        $user = $userTable->fetchOne(['email' => $identity]);
        return $user;
    }

    public function callBack($hash, $password)
    {
        $bCrypt = new Bcrypt();
        return $bCrypt->verify($password, $hash);
    }
}