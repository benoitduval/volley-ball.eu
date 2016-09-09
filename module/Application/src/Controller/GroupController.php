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

class GroupController extends AbstractController
{

    public function createAction()
    {
        $groupForm  = new Form\Group;
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $config     = $this->getContainer()->get('config');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $groupForm->setData($request->getPost());
            if ($groupForm->isValid()) {

                $data               = $groupForm->getData();
                $group              = New Model\Group();
                $group->name        = ucfirst($data['name']);
                $group->userId      = $this->getUser()->id;
                $group->userIds     = json_encode([$this->getUser()->id]);
                $group->description = $data['description'];
                $group->info        = $data['info'];
                $brand              = $group->initBrand();

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
        $groupTable     = $this->getContainer()->get(TableGateway\Group::class);
        $group          = $groupTable->find($id);
        $config         = $this->getContainer()->get('config');
        $baseUrl        = $config['baseUrl'];
        $result         = [];

        $guestTable     = $this->getContainer()->get(TableGateway\Guest::class);
        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $eventTable     = $this->getContainer()->get(TableGateway\Event::class);

        foreach ($userGroupTable->fetchAll(['userId' => $this->getUser()->id]) as $userGroup) {
            $groups[$userGroup->groupId] = $groupTable->find($userGroup->groupId); 
        }

        $guests = $guestTable->fetchAll([
            'userId'  => $this->getUser()->id,
            'groupId' => $id,
        ]);

        $counters = [];
        foreach ($guests as $guest) {
            $event    = $eventTable->find($guest->eventId);
            $counters = $guestTable->getCounters($guest->eventId);
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

}
