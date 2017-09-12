<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Application\Model;
use Application\TableGateway;


class AbstractController extends AbstractActionController
{
    protected $_container;
    protected $_user       = null;
    protected $_userGroups = [];

    public    $userTable;
    public    $userGroupTable;
    public    $joinTable;
    public    $groupTable;
    public    $recurentTable;

    public function __construct(ContainerInterface $container, $tables, $user = false)
    {
        $this->_container       = $container;
        $this->_user            = $user;
        $this->_userGroups      = $this->getUserGroups();

        foreach ($tables as $name => $obj) {
            $name .= 'Table';
            $this->$name = $obj;
        }
    }

    public function get($name, $options = [])
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
            if ($result = $groupTable->getAllByUserId($this->_user->id))
            foreach ($result as $group) {
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
        $this->layout()->badges = $this->_getBadges();
        return parent::onDispatch($e);
    }

    protected function _getBadges()
    {
        $result['count'] = 0;
        if ($this->getUser()) {
            $key = 'badges.comments.user.' . $this->getUser()->id;
            $cached = $this->get('memcached')->getItem($key);
            if ($cached = $this->get('memcached')->getItem($key)) {
                foreach ($cached as $data) {
                    $result['count'] += $data['count'];
                    $result['comments'][] = [
                        'label' => '<span class="badge">' . $data['count'] . '</span> ' . $data['name'] . ' (' . $data['date'].')',
                        'link' => '#',
                        'id' => $data['id']
                    ];
                }
            }

            // $userGroups = [];
            // foreach ($this->getUserGroups() as $group) {
            //     $userGroups[$group->id] = $group;
            // }

            // if ($userGroups) {
            //     $today = new \DateTime('today midnight');
            //     $eventTable = $this->get(TableGateway\Event::class);
            //     $guestTable = $this->get(TableGateway\Guest::class);
            //     $events = $eventTable->fetchAll([
            //         'groupId'   => array_keys($userGroups),
            //         'date >= ?' => $today->format('Y-m-d H:i:s')
            //     ], 'date ASC');

            //     foreach ($events as $key => $event) {
            //         $guest = $guestTable->fetchOne([
            //             'userId'  => $this->getUser()->id,
            //             'eventId' => $event->id
            //         ]);

            //         if ($guest->response == Model\Guest::RESP_INCERTAIN || $guest->response == Model\Guest::RESP_NO_ANSWER) {
            //             $result['count'] ++;
            //             $result['events'][] = [
            //                 'label' => $event->name,
            //                 'link'  => '/event/detail/' . $event->id,
            //                 'id'    => $event->id
            //             ];
            //         }
            //     }
            // }
        }
        return $result;
    }
}
