<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\Form;
use Application\TableGateway;

class EventController extends AbstractController
{

    public function indexAction()
    {
        // to do : change this to a super admin account
        if ($this->getUser()->id == 1) {
            $events  = $this->eventTable->fetchAll();

            $this->layout()->setTemplate('admin/layout/layout.phtml');
            return new ViewModel([
                'events' => $events,
            ]);
        }
    }

    // public function detailAction()
    // {
    //     $id         = $this->params('id', null);

    //     $eventTable = $this->get(TableGateway\Event::class);
    //     $event      = $eventTable->find($id);

    //     $groupTable = $this->get(TableGateway\Group::class);
    //     $group      = $groupTable->find($event->groupId);

    //     $guestTable = $this->get(TableGateway\Guest::class);
    //     $guests     = $guestTable->fetchAll(['eventId' => $event->id]);

    //     $userTable  = $this->get(TableGateway\User::class);
    //     foreach ($guests as $guest) {
    //         $users[$guest->userId] = $userTable->find($guest->userId);
    //     }

    //     $form = new Form\Event;
    //     $form->setData($event->toArray());
    //     $request = $this->getRequest();
    //     if ($request->isPost()) {
    //         $data = $request->getPost();
    //         $data['email'] = $user->email;
    //         $form->setData($request->getPost());
    //         if ($form->isValid()) {
    //             $data = $form->getData();
    //             foreach ($data as $key => $value) {
    //                 if (!$value) unset($data[$key]);
    //             }
    //             $data['id'] = $user->id;
    //             $userTable->save($data);
    //         }
    //     }

    //     $this->layout()->setTemplate('admin/layout/layout.phtml');
    //     return new ViewModel([
    //         'event'  => $event,
    //         'group'  => $group,
    //         'guests' => $guests,
    //         'users'  => $users,
    //         'form'   => $form,
    //     ]);
    // }
}