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
            $groupTable = $this->get(TableGateway\Group::class);
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            if($this->_group = $groupTable->find($this->_id)) {
                $this->_isAdmin  = $userGroupTable->isAdmin($this->getUser()->id, $this->_id);
                $this->_isMember = $userGroupTable->isMember($this->getUser()->id, $this->_id);
            }
        }
        return parent::onDispatch($e);
    }

    public function createAction()
    {
        if ($this->getUser()) {
            $groupForm      = new Form\Group;
            $groupTable     = $this->get(TableGateway\Group::class);
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $config         = $this->get('config');

            $request = $this->getRequest();
            if ($request->isPost()) {

                $groupForm->setData($request->getPost());
                if ($groupForm->isValid()) {

                    $data               = $groupForm->getData();
                    $group              = New Model\Group();
                    $data['name']       = ucfirst($data['name']);
                    $data['brand']      = $group->initBrand($data['name']);

                    $mapService = $this->get(Service\Map::class);
                    if ($coords = $mapService->getCoordinates($data['address'])) {
                        $data = array_merge($data, $coords);
                    }

                    $group->exchangeArray($data);
                    $groupId            = $groupTable->save($group);
                    $userGroup          = New Model\UserGroup();
                    $userGroup->userId  = $this->getUser()->id;
                    $userGroup->groupId = $groupId;
                    $userGroup->admin   = 1;
                    $userGroupTable->save($userGroup);

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

    public function detailAction()
    {
        if ($this->_group && $this->_isMember) {
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $groupTable     = $this->get(TableGateway\Group::class);
            $guestTable     = $this->get(TableGateway\Guest::class);
            $eventTable     = $this->get(TableGateway\Event::class);
            $config         = $this->get('config');
            $baseUrl        = $config['baseUrl'];
            $result         = [];

            foreach ($userGroupTable->fetchAll(['userId' => $this->getUser()->id]) as $userGroup) {
                $groups[$userGroup->groupId] = $groupTable->find($userGroup->groupId); 
            }

            $today = new \DateTime('today midnight');
            $events = $eventTable->fetchAll([
                'groupId'   => $this->_id,
                'date >= ?' => $today->format('Y-m-d H:i:s')
            ], 'date ASC');

            $counters = [];
            foreach ($events as $event) {
                $eventIds[] = $event->id;
                $userEvents[$event->id] = $event;

                $guest = $guestTable->fetchOne([
                    'userId'  => $this->getUser()->id,
                    'eventId' => $event->id
                ]);

                $counters = $guestTable->getCounters($event->id);
                $result[$guest->id] = [
                    'group'   => $groups[$guest->groupId],
                    'event'   => $event,
                    'guest'   => $guest,
                    'ok'      => $counters[Model\Guest::RESP_OK],
                    'no'      => $counters[Model\Guest::RESP_NO],
                    'perhaps' => $counters[Model\Guest::RESP_INCERTAIN],
                    'date'    => \DateTime::createFromFormat('Y-m-d H:i:s', $event->date),
                ];
            }

            return new ViewModel([
                'events'     => $result,
                'user'       => $this->getUser(),
                'groups'     => $groups,
                'group'      => $this->_group,
                'isAdmin'    => $this->_isAdmin,
            ]);
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
                    $groupTable = $this->get(TableGateway\Group::class);
                    $data       = $form->getData();

                    $mapService = $this->get(Service\Map::class);
                    if ($coords = $mapService->getCoordinates($data['address'])) {
                        $data = array_merge($data, $coords);
                    }

                    $this->_group->exchangeArray($data);
                    $groupTable->save($this->_group);
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
        $brand          = $this->params('brand');
        $subscribe      = $this->params('subscribe', null);
        $groupTable     = $this->get(TableGateway\Group::class);
        $joinTable      = $this->get(TableGateway\Join::class);
        $userTable      = $this->get(TableGateway\User::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);
        $group          = $groupTable->fetchOne(['brand' => $brand]);

        if (!$this->getUser()) {
            $signInForm = new Form\SignIn();
            $signUpForm = new Form\SignUp();

            return new ViewModel(array(
                'signInForm' => $signInForm,
                'signUpForm' => $signUpForm,
                'user'       => $this->getUser(),
                'group'      => $group,
            ));
        } else {
            if ($subscribe) {

                $join = new Model\Join();
                $join->exchangeArray([
                    'userId'   => $this->getUser()->id,
                    'groupId'  => $group->id,
                    'response' => Model\Join::RESPONSE_WAITING
                ]);

                $joinTable->save($join);

                $mail   = $this->get(MailService::class);
                $config = $this->get('config');

                // TODO - add admin emails
                $mail->addBcc('benoit.duval.pro@gmail.com');
                $mail->setSubject('[' . $group->name . '] Une personne souhaite rejoindre le groupe');
                $mail->setTemplate(MailService::TEMPLATE_GROUP, array(
                    'title'     => 'Demande d\'adhésion',
                    'subtitle'  => $group->name,
                    'user'      => $this->getUser()->getFullname(),
                    'userId'    => $this->getUser()->id,
                    'username'  => $this->getUser()->getFullname(),
                    'groupname' => $group->name,
                    'groupId'   => $group->id,
                    'ok'        => Model\Group::RESPONSE_OK,
                    'no'        => Model\Group::RESPONSE_NO,
                    'baseUrl'   => $config['baseUrl']
                ));
                $mail->send();
                $this->flashMessenger()->addMessage('Votre demande pour rejoindre le groupe <b>' . $group->name . '</b> à bien été enregistrer. Vous serez notifier quand cette demande aura été traitée.<br> merci de votre patience.');
                $this->redirect()->toRoute('group-welcome', ['brand' => $group->brand]);
            }
        }

        $this->layout()->opacity = true;
        return new ViewModel([
            'member' => $userGroupTable->isMember($this->getUser()->id, $group->id),
            'user'   => $this->getUser(),
            'group'  => $group,
        ]);
    }

    public function historyAction()
    {
        if ($this->_group && $this->_isMember) {
            $eventTable = $this->get(TableGateway\Event::class);
            $events = $eventTable->fetchAll([
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
            $userTable      = $this->get(TableGateway\User::class);
            $joinTable      = $this->get(TableGateway\Join::class);

            $joins = $joinTable->fetchAll([
                'groupId' => $this->_id
            ]);

            $joinUserIds = [];
            $userJoin    = [];
            foreach ($joins as $join) {
                $joinUserIds[] = $join->userId;
            }
            if (!empty($joinUserIds)) $userJoin = $userTable->fetchAll(['id' => $joinUserIds]);

            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroups = $userGroupTable->fetchAll([
                'groupId' => $this->_id
            ]);

            $adminIds = [];
            foreach ($userGroups as $userGroup) {
                if ($userGroup->admin) $adminIds[] = $userGroup->userId;
                $userIds[]  = $userGroup->userId;
            }

            $users = $userTable->fetchAll(['id' => $userIds]);

            return new ViewModel([
                'adminIds' => $adminIds,
                'isAdmin' => $this->_isAdmin,
                'users' => $users,
                'group' => $this->_group,
                'join'  => $userJoin,
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function deleteUserAction()
    {
        $userId    = $this->params('userId');
        $userTable = $this->get(TableGateway\User::class);

        if ($userId && $this->_group && $this->_isAdmin) {
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroup = $userGroupTable->fetchOne([
                'groupId' => $this->_id,
                'userId'  => $userId
            ]);

            $eventTable = $this->get(TableGateway\Event::class);
            $events = $eventTable->fetchAll([
                'date > NOW()',
                'groupId' => $this->_group->id
            ]);
            $guestTable = $this->get(TableGateway\Guest::class);
            foreach ($events as $event) {
                $guest = $guestTable->fetchOne([
                    'userId'  => $userId,
                    'groupId' => $this->_group->id,
                    'eventId' => $event->id,
                ]);
                $guestTable->delete($guest);
            }
            $userGroupTable->delete($userGroup);

            $this->flashMessenger()->addMessage('Utilisateur supprimé.');
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
        }
        $this->redirect()->toRoute('group', ['action' => 'users', 'id' => $this->_group->id]);
    }

    public function addUserAction()
    {
        $userId    = $this->params('userId');
        $userTable = $this->get(TableGateway\User::class);

        if ($userId && $this->_group && $this->_isAdmin) {
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroup = new Model\UserGroup;
            $userGroup->exchangeArray([
                'groupId' => $this->_group->id,
                'userId'  => $userId,
                'admin'   => Model\UserGroup::MEMBER,
            ]);

            $userGroupTable->save($userGroup);

            $eventTable = $this->get(TableGateway\Event::class);
            $events = $eventTable->fetchAll([
                'date > NOW()',
                'groupId' => $this->_group->id
            ]);

            $guestTable = $this->get(TableGateway\Guest::class);
            foreach ($events as $event) {
                $guest = new Model\Guest;
                $guest->exchangeArray([
                    'userId'  => $userId,
                    'groupId' => $this->_group->id,
                    'eventId' => $event->id,
                    'response' => Model\Guest::RESP_NO_ANSWER,
                ]);
                $guestTable->save($guest);
            }

            $joinTable = $this->get(TableGateway\Join::class);
            $join = $joinTable->fetchOne([
                'userId'  => $userId,
                'groupId' => $this->_group->id,
            ]);
            $joinTable->delete($join);

            $this->flashMessenger()->addMessage('Utilisateur ajouté.');
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
        }
        $this->redirect()->toRoute('group', ['action' => 'users', 'id' => $this->_group->id]);
    }
}
