<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;
use Application\Form;
use Application\Model;
use Application\Service\MailService;
use Application\Service;

class GroupController extends AbstractController
{

    protected $_group    = null;
    protected $_id       = null;
    protected $_isAdmin  = false;
    protected $_isMembre = false;
    protected $_groups   = [];

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if ($this->_id = $this->params('id')) {
            $this->groupTable = $this->get(TableGateway\Group::class);
            $this->userGroupTable = $this->get(TableGateway\UserGroup::class);
            if ($this->_group = $this->groupTable->find($this->_id)) {
                $this->_isAdmin  = $this->userGroupTable->isAdmin($this->getUser()->id, $this->_id);
                $this->_isMember = $this->userGroupTable->isMember($this->getUser()->id, $this->_id);
            }
        }
        return parent::onDispatch($e);
    }

    public function createAction()
    {
        if ($this->getUser()) {
            $groupForm      = new Form\Group;
            $config         = $this->get('config');

            $request = $this->getRequest();
            if ($request->isPost()) {

                $groupForm->setData($request->getPost());
                if ($groupForm->isValid()) {

                    $data               = $groupForm->getData();
                    $data['name']       = ucfirst($data['name']);
                    $data['brand']      = Model\Group::initBrand($data['name']);

                    try {
                        $mapService = $this->get(Service\Map::class);
                        if ($coords = $mapService->getCoordinates($data['address'])) {
                            $data = array_merge($data, $coords);
                        }
                    } catch (\Exception $e) {
                    }

                    $group = $this->groupTable->save($data);

                    $userGroup = $this->userGroupTable->save([
                        'userId'  => $this->getUser()->id,
                        'groupId' => $group->id,
                        'admin'   => 1,
                    ]);

                    $this->flashMessenger()->addMessage('
                        Votre groupe est maintenant actif.<br/>
                        Depuis cette page, le bouton d\'action en bas à droite vous permet de:
                        <ul>
                            <li>Créer un évènement ponctuel (match)</li>
                            <li>Créer un évènement récurrent (entrainement)</li>
                            <li>Partager votre groupe</li>
                            <li>Gérer les informations du groupe</li>
                            <li>Gérer les demandes pour rejoindre le groupe</li>
                            <li>Gérer les membres ainsi que leurs permissions</li>
                        </ul>
                    ');
                    return $this->redirect()->toRoute('group-welcome', ['brand' => $group->brand]);
                }
            }

            $baseUrl = $config['baseUrl'];

            return new ViewModel(array(
                'form'    => $groupForm,
                'user'    => $this->getUser(),
                'group'   => isset($group) ? $group : '',
                'baseUrl' => $baseUrl,
            ));
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function editAction()
    {
        if ($this->_group && $this->_isAdmin) {
            $form       = new Form\Group;
            $form->setData($this->_group->toArray());
            $request    = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data       = $form->getData();

                    $this->_group->exchangeArray($data);
                    $this->groupTable->save($this->_group);
                }
                $this->flashMessenger()->addMessage('Votre groupe a bien été modifié.');
                return $this->redirect()->toRoute('group-welcome', ['brand' => $this->_group->brand]);

            }

            return new ViewModel(array(
                'form'    => $form,
                'user'    => $this->getUser(),
                'group'   => isset($this->_group) ? $this->_group : '',
                'isAdmin' => $this->_isAdmin,
            ));
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            return $this->redirect()->toRoute('home');
        }
    }

    public function welcomeAction()
    {
        $isAdmin        = false;
        $isMember       = false;
        $brand          = $this->params('brand');
        $subscribe      = $this->params()->fromQuery('subscribe', null);
        $events         = [];
        $result         = [];
        $matches        = [];

        $group          = $this->groupTable->fetchOne(['brand' => $brand]);
        $users          = $this->userTable->getUsersByGroupId($group->id);
        $form           = new Form\Share();

        $disponibilityLast = [];
        $scoresLast = $scoresCurrent = [
            '3 / 0' => 0,
            '3 / 1' => 0,
            '3 / 2' => 0,
            '0 / 3' => 0,
            '1 / 3' => 0,
            '2 / 3' => 0,
        ];

        $eventsCount = $this->eventTable->count(['groupId' => $group->id]);

        if ($this->getUser() && $this->userGroupTable->isMember($this->getUser()->id, $group->id)) {

            $y = date('Y');
            $september = strtotime($y . '-09-01');
            if (time() < $september) {
                $lastStart    = strtotime($y . '-09-01 -2years');
                $lastEnd      = strtotime($y . '-08-31 -1years');
                $currentStart = strtotime($y . '-09-01 -1years');
                $currentEnd   = strtotime($y . '-08-31');
            } else {
                $lastStart    = strtotime($y . '-09-01 -1years');
                $lastEnd      = strtotime($y . '-08-31');
                $currentStart = strtotime($y . '-09-01');
                $currentEnd   = strtotime($y . '-08-31  +1years');
            }

            $lastDisp = $this->groupTable->getGroupDisponibility($group->id, $lastStart, $lastEnd);
            $currentDisp = $this->groupTable->getGroupDisponibility($group->id, $currentStart, $currentEnd);

            $eventsLast = $this->eventTable->fetchAll([
                'groupId'  => $group->id,
                'date > ?' => date('Y-m-d H:i:s', $lastStart),
                'date < ?' => date('Y-m-d H:i:s', $lastEnd),
            ], 'date DESC');

            $eventsCurrent = $this->eventTable->fetchAll([
                'groupId'  => $group->id,
                'date > ?' => date('Y-m-d H:i:s', $currentStart),
                'date < ?' => date('Y-m-d H:i:s', $currentEnd),
            ], 'date DESC');

            foreach ($eventsLast as $event) {
                $eventDate = \Datetime::createFromFormat('Y-m-d H:i:s', $event->date);

                if (!isset($disponibilityLast[$eventDate->format('m')])) {
                    $count = $this->guestTable->count([
                        'eventId'  => $event->id,
                        'response' => 3
                    ]);
                }

                if ($match = $this->matchTable->fetchOne(['eventId' => $event->id])) {
                    $scoresLast[$match->sets] ++;
                }
            }

            foreach ($eventsCurrent as $event) {
                $eventDate = strtotime($event->date);
                if ($match = $this->matchTable->fetchOne(['eventId' => $event->id])) {
                    $matches[$event->id] = [
                        'event' => $event,
                        'result' => $match
                    ];
                    $scoresCurrent[$match->sets] ++;
                }
            }
        }

        foreach (['3 / 0', '3 / 1', '3 / 2', '2 / 3', '1 / 3', '0 / 3'] as $index) {
            $inlineScoreLast[] = $scoresLast[$index];
            $inlineScoreCurrent[] = $scoresCurrent[$index];
        }
        $inlineScoreLast    = json_encode($inlineScoreLast);
        $inlineScoreCurrent = json_encode($inlineScoreCurrent);

        $isAdmin = $this->userGroupTable->isAdmin($this->getUser()->id, $group->id);
        $isMember = $this->userGroupTable->isMember($this->getUser()->id, $group->id);
        $this->layout()->group = $group;
        $this->layout()->isAdmin = $isAdmin;

        return new ViewModel([
            'user'          => $this->getUser(),
            'group'         => $group,
            'events'        => $events,
            'matches'       => array_slice($matches, 0, 5),
            'users'         => $users,
            'isMember'      => $isMember,
            'isAdmin'       => $isAdmin,
            'form'          => $form,
            'scoresLast'    => $inlineScoreLast,
            'scoresCurrent' => $inlineScoreCurrent,
            'lastDisp'      => $lastDisp,
            'currentDisp'   => $currentDisp,
            'eventsCount'   => $eventsCount,
        ]);
    }

    public function historyAction()
    {
        if ($this->_group && $this->_isMember) {
            $this->eventTable = $this->get(TableGateway\Event::class);
            $events = $this->eventTable->fetchAll([
                'groupId' => $this->_id
            ], 'date DESC');

            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'events' => $events,
                'group'  => $this->_group,
                'isAdmin' => $this->_isAdmin
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function usersAction()
    {
        if ($this->_group && $this->_isAdmin) {
            $this->userTable      = $this->get(TableGateway\User::class);
            $this->joinTable      = $this->get(TableGateway\Join::class);

            $joins = $this->joinTable->fetchAll([
                'groupId' => $this->_id
            ]);

            $joinUserIds = [];
            $userJoin    = [];
            foreach ($joins as $join) {
                $joinUserIds[] = $join->userId;
            }
            if (!empty($joinUserIds)) $userJoin = $this->userTable->fetchAll(['id' => $joinUserIds]);

            $this->userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroups = $this->userGroupTable->fetchAll([
                'groupId' => $this->_id
            ]);

            $adminIds = [];
            foreach ($userGroups as $userGroup) {
                if ($userGroup->admin) $adminIds[] = $userGroup->userId;
                $userIds[]  = $userGroup->userId;
            }

            $users = $this->userTable->fetchAll(['id' => $userIds]);

            return new ViewModel([
                'adminIds' => $adminIds,
                'isAdmin'  => $this->_isAdmin,
                'users'    => $users,
                'group'    => $this->_group,
                'joins'    => $userJoin,
                'user'     => $this->getUser()
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function deleteUserAction()
    {
        $userId    = $this->params('userId');
        $this->userTable = $this->get(TableGateway\User::class);

        if ($userId && $this->_group && $this->_isAdmin) {
            $this->userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroup = $this->userGroupTable->fetchOne([
                'groupId' => $this->_id,
                'userId'  => $userId
            ]);

            $this->eventTable = $this->get(TableGateway\Event::class);
            $events = $this->eventTable->fetchAll([
                'date > NOW()',
                'groupId' => $this->_group->id
            ]);
            $this->guestTable = $this->get(TableGateway\Guest::class);
            foreach ($events as $event) {
                $guest = $this->guestTable->fetchOne([
                    'userId'  => $userId,
                    'groupId' => $this->_group->id,
                    'eventId' => $event->id,
                ]);
                $this->guestTable->delete($guest);
            }
            $this->userGroupTable->delete($userGroup);

            $this->flashMessenger()->addMessage('Utilisateur supprimé.');
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
        }
        $this->redirect()->toRoute('group', ['action' => 'users', 'id' => $this->_group->id]);
    }

    public function addUserAction()
    {
        $userId    = $this->params('userId');
        $this->userTable = $this->get(TableGateway\User::class);

        if ($userId && $this->_group && $this->_isAdmin) {
            $this->userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroup = $this->userGroupTable->save([
                'groupId' => $this->_group->id,
                'userId'  => $userId,
                'admin'   => Model\UserGroup::MEMBER,
            ]);

            $this->eventTable = $this->get(TableGateway\Event::class);
            $events = $this->eventTable->fetchAll([
                'date > NOW()',
                'groupId' => $this->_group->id
            ]);

            $this->guestTable  = $this->get(TableGateway\Guest::class);
            $absentTable = $this->get(TableGateway\Absent::class);
            foreach ($events as $event) {
                $absent = $absentTable->fetchOne([
                    'userId'     => $userId,
                    '`from` < ?' => $event->date,
                    '`to` > ?'   => $event->date,
                ]);

                $response = $absent ? Model\Guest::RESP_NO : Model\Guest::RESP_NO_ANSWER;
                $guest = $this->guestTable->save([
                    'userId'  => $userId,
                    'groupId' => $this->_group->id,
                    'eventId' => $event->id,
                    'response' => $response,
                ]);
            }

            $this->joinTable = $this->get(TableGateway\Join::class);
            $join = $this->joinTable->fetchOne([
                'userId'  => $userId,
                'groupId' => $this->_group->id,
            ]);
            $this->joinTable->delete($join);

            $this->get('memcached')->removeItem('user.groups.' . $userId);
            $this->flashMessenger()->addMessage('Utilisateur ajouté.');
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            return $this->redirect()->toRoute('home');
        }
        $this->redirect()->toRoute('group', ['action' => 'users', 'id' => $this->_group->id]);
    }

    public function deleteAction()
    {
        $delete = $this->params()->fromQuery('delete', false);

        if ($this->_group && $this->_isAdmin) {
            if ($delete) {
                $this->eventTable     = $this->get(TableGateway\Event::class);
                $this->commentTable   = $this->get(TableGateway\Comment::class);
                $this->userGroupTable = $this->get(TableGateway\UserGroup::class);
                $this->joinTable      = $this->get(TableGateway\Join::class);
                $this->matchTable     = $this->get(TableGateway\Match::class);
                $this->groupTable     = $this->get(TableGateway\Group::class);
                $this->guestTable     = $this->get(TableGateway\Guest::class);

                foreach ($this->eventTable->fetchAll(['groupId' => $this->_id]) as $event) {
                    $eventIds[] = $event->id;
                }

                if ($eventIds) {
                    $this->commentTable->delete(['eventId' => $eventIds]);
                    $this->matchTable->delete(['eventId' => $eventIds]);
                    $this->guestTable->delete(['eventId' => $eventIds]);
                }
                $this->userGroupTable->delete(['groupId' => $this->_id]);
                $this->eventTable->delete(['groupId' => $this->_id]);
                $this->joinTable->delete(['groupId' => $this->_id]);
                $this->groupTable->delete(['id' => $this->_id]);

                $this->flashMessenger()->addMessage('Le groupe a bien été supprimé. Vous avez été redirigé sur la page d\accueil.');
                return $this->redirect()->toRoute('home');
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            return $this->redirect()->toRoute('home');
        }

        return new ViewModel([
            'isAdmin' => $this->_isAdmin,
            'group' => $this->_group,
            'user' => $this->getUser()
        ]);
    }
}
