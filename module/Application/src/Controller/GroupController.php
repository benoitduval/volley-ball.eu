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

    public function createAction()
    {
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
                return $this->redirect()->toRoute('home');
            }
        }

        $baseUrl = $config['baseUrl'];

        return new ViewModel(array(
            'form'    => $groupForm,
            'user'    => $this->getUser(),
            'group'   => isset($group) ? $group : '',
            'baseUrl' => $baseUrl,
        ));
    }

    public function detailAction()
    {
        $id             = (int) $this->params()->fromRoute('id');
        $groupTable     = $this->get(TableGateway\Group::class);
        $group          = $groupTable->find($id);
        $config         = $this->get('config');
        $baseUrl        = $config['baseUrl'];
        $result         = [];

        $guestTable     = $this->get(TableGateway\Guest::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);
        $eventTable     = $this->get(TableGateway\Event::class);

        foreach ($userGroupTable->fetchAll(['userId' => $this->getUser()->id]) as $userGroup) {
            $groups[$userGroup->groupId] = $groupTable->find($userGroup->groupId); 
        }

        $today = new \DateTime('today midnight');
        $events = $eventTable->fetchAll([
            'groupId'   => $id,
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

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'events'     => $result,
            'user'       => $this->getUser(),
            'groups'     => $groups,
            'group'      => $group,
        ]);
    }

    public function editAction()
    {
        $id         = (int) $this->params()->fromRoute('id');
        $groupTable = $this->get(TableGateway\Group::class);
        $group      = $groupTable->find($id);
        $form       = new Form\Group;

        $form->setData($group->toArray());
        $request    = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $group->exchangeArray($data);
                $groupTable->save($group);
            }
            $this->flashMessenger()->addMessage('Votre groupe a bien été modifié.');
            $this->redirect()->toRoute('group', ['action' => 'detail', 'id' => $id]);
        }

        $this->layout()->user = $this->getUser();
        return new ViewModel(array(
            'form'    => $form,
            'user'    => $this->getUser(),
            'group'   => isset($group) ? $group : '',
        ));
    }

    public function welcomeAction()
    {
        $brand      = $this->params()->fromRoute('brand');
        $subscribe  = $this->params()->fromQuery('subscribe', null);
        $groupTable = $this->get(TableGateway\Group::class);
        $joinTable  = $this->get(TableGateway\Join::class);
        $userTable  = $this->get(TableGateway\User::class);
        $userGroupTable  = $this->get(TableGateway\UserGroup::class);
        $group      = $groupTable->fetchOne(['brand' => $brand]);

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
                    'pitch'     => $group->name,
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
        $this->layout()->user   = $this->getUser();
        return new ViewModel([
            'member' => $userGroupTable->isMember($this->getUser()->id, $group->id),
            'user'   => $this->getUser(),
            'group'  => $group,
        ]);
    }

    public function historyAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $eventTable = $this->get(TableGateway\Event::class);
        $events = $eventTable->fetchAll([
            'groupId' => $id
        ], 'date DESC');

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'events' => $events,
        ]);
    }

    public function usersAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $userGroupTable = $this->get(TableGateway\UserGroup::class);
        $joinTable      = $this->get(TableGateway\Join::class);
        $userTable      = $this->get(TableGateway\User::class);
        $groupTable      = $this->get(TableGateway\Group::class);

        $group = $groupTable->find($id);

        $joins = $joinTable->fetchAll([
            'groupId' => $id
        ]);

        $joinUserIds = [];
        $userJoin = [];
        foreach ($joins as $join) {
            $joinUserIds[] = $join->userId;
        }
        if (!empty($joinUserIds)) $userJoin = $userTable->fetchAll(['id' => $joinUserIds]);

        $userGroups = $userGroupTable->fetchAll([
            'groupId' => $id
        ]);

        $adminIds = [];
        foreach ($userGroups as $userGroup) {
            if ($userGroup->admin) $adminIds[] = $userGroup->userId;
            $userIds[]  = $userGroup->userId;
        }

        $users = $userTable->fetchAll(['id' => $userIds]);

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'adminIds' => $adminIds,
            'users' => $users,
            'group' => $group,
            'join'  => $userJoin,
        ]);
    }

}
