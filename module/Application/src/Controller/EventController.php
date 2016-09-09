<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;
use Application\Service\MailService;

class EventController extends AbstractController
{
    public function createAction()
    {
        $groupId    = $this->params()->fromRoute('id');

        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $userTable  = $this->getContainer()->get(TableGateway\User::class);

        $group = $groupTable->find($groupId);

        $form = new Form\Event();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post  = $request->getPost();
            $match = isset($post->match);
            unset($post->match);

            $form->setData($post);
            if ($form->isValid()) {

                $data = $form->getData();
                $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                $data['date']    = $date->format('Y-m-d H:i:s');
                $data['userId']  = $this->getUser()->id;
                $data['groupId'] = $groupId;

                $mapService = $this->getContainer()->get(Service\Map::class);
                $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                if ($coords = $mapService->getCoordinates($address)) {
                    $data = array_merge($data, $coords);
                }

                $event = new Model\Event();
                $event->exchangeArray($data);
                $event->id = $eventTable->save($event);

                // Create guest for this new event
                $emails = [];
                foreach (json_decode($group->userIds) as $id) {

                    $user = $userTable->find($id);
                    $emails[] = $user->email;

                    $guest = new Model\Guest();
                    $guest->exchangeArray([
                        'eventId'  => $event->id,
                        'userId'   => $id,
                        'response' => Model\Guest::RESP_NO_ANSWER,
                        'groupId'  => $groupId,
                    ]);

                    $guestTable->save($guest);
                    unset($guest);
                }

                // send emails
                $mail   = $this->getContainer()->get(MailService::class);
                $config = $this->getContainer()->get('config');
                $mail->addBcc($emails);
                $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $date->format('l d F \à H\hi'));

                $mail->setTemplate(MailService::TEMPLATE_EVENT, [
                    'pitch'     => '$pitch',
                    'title'     => $event->name . ' <br /> ' . $date->format('l d F \à H\hi'),
                    'subtitle'  => $group->name,
                    'name'      => $event->place,
                    'zip'       => $event->zipCode,
                    'city'      => $event->city,
                    'eventId'   => $event->id,
                    'date'      => $date->format('l d F \à H\hi'),
                    'day'       => $date->format('d'),
                    'month'     => $date->format('F'),
                    'ok'        => Model\Guest::RESP_OK,
                    'no'        => Model\Guest::RESP_NO,
                    'perhaps'   => Model\Guest::RESP_INCERTAIN,
                    'comment'   => $data['comment'],
                    'baseUrl'   => $config['baseUrl']
                ]);
                $mail->send();

                if ($match) {
                    $match = new Model\Match();
                    $match->eventId = $event->id;
                    $matchTable = $this->getContainer()->get(TableGateway\Match::class);
                    $matchTable->save($match);
                }

                $this->flashMessenger()->addMessage('Votre évènement a bien été créé.
                    Les notifications ont été envoyés aux membres du groupe.');
                $this->redirect()->toRoute('home');
            } else {
                \Zend\Debug\Debug::dump("nop");die;
            }

        }

        return new ViewModel([
            'group'  => $group,
            'form'   => $form,
            'user'   => $this->getUser(),
        ]);

    }

    public function detailAction()
    {
        $eventId        = $this->params()->fromRoute('id');
        $eventTable     = $this->getContainer()->get(TableGateway\Event::class);
        $groupTable     = $this->getContainer()->get(TableGateway\Group::class);
        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $guestTable     = $this->getContainer()->get(TableGateway\Guest::class);
        $userTable      = $this->getContainer()->get(TableGateway\User::class);
        $commentTable   = $this->getContainer()->get(TableGateway\Comment::class);
        $matchTable     = $this->getContainer()->get(TableGateway\Match::class);

        $form = new Form\Comment();

        $event     = $eventTable->find($eventId);

        $match     = $matchTable->fetchOne(['eventId' => $event->id]);
        $comments  = $commentTable->fetchAll($eventId);
        $group     = $groupTable->find($event->groupId);
        $isMember  = $userGroupTable->isAdmin($this->getUser()->id, $group->id);
        $isAdmin   = false;
        if ($isMember) {
            $isAdmin = $userGroupTable->isAdmin($this->getUser()->id, $group->id);
        }

        $counters  = $guestTable->getCounters($eventId);
        $guests    = $guestTable->fetchAll(['eventId' => $eventId]);
        $eventDate = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data            = $form->getData();
                $data['date']    = date('Y-m-d H:i:s');
                $data['eventId'] = $eventId;
                $data['userId']  = $this->getUser()->id;

                $comment = new Model\Comment();
                $comment->exchangeArray($data);
                $comment->id = $commentTable->save($comment);

                $this->flashMessenger()->addMessage('Votre commentaire a bien été enregistré.');
                $this->redirect()->toRoute('event', ['action' => detail, 'id' => $eventId]);
            }
        }

        $availability    = [
            Model\Guest::RESP_NO_ANSWER => [],
            Model\Guest::RESP_OK        => [],
            Model\Guest::RESP_NO        => [],
            Model\Guest::RESP_INCERTAIN => [],
        ];

        foreach ($guests as $guest) {
            $users[$guest->userId] = $userTable->find($guest->userId);
            $availability[$guest->response][] = $users[$guest->userId];
        }

        $result = [];
        foreach ($comments as $comment) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $comment->date);
            $result[$comment->id]['date'] = $date->format('d F Y \à H:i');
            $result[$comment->id]['author'] = $users[$comment->userId];
            $result[$comment->id]['comment'] = $comment->comment;
        }

         $test = $guestTable->getCounters($eventId);

        $config     = $this->getContainer()->get('config');
        $baseUrl    = $config['baseUrl'];

        $this->layout()->opacity = true;
        return new ViewModel([
            'match'    => $match,
            'counters' => $test,
            'comments' => $result,
            'event'    => $event,
            'form'     => $form,
            'group'    => $group,
            'users'    => $availability,
            'user'     => $this->getUser(),
            'date'     => $eventDate,
            'isAdmin'  => $isAdmin,
            'isMember' => $isMember,
        ]);
    }

    public function editAction()
    {
        $eventId    = $this->params()->fromRoute('id');

        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $userTable  = $this->getContainer()->get(TableGateway\User::class);

        $event = $eventTable->find($eventId);
        $group = $groupTable->find($event->groupId);

        $form = new Form\Event();
        $form->setData($event->toArray());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post    = $request->getPost()->toArray();

            $form->setData($request->getPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                $data['date']    = $date->format('Y-m-d H:i:s');
                $mapService = $this->getContainer()->get(Service\Map::class);
                $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                if ($coords = $mapService->getCoordinates($address)) {
                    $data = array_merge($data, $coords);
                }

                $event->exchangeArray($data);
                $eventTable->save($event);

                // send emails
                // $mail   = $this->getContainer()->get(MailService::class);
                // $config = $this->getContainer()->get('config');
                // $mail->addBcc($emails);
                // $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $date->format('l d F \à H\hi'));

                // $mail->setTemplate(MailService::TEMPLATE_EVENT, [
                //     'pitch'     => '$pitch',
                //     'title'     => $event->name . ' <br /> ' . $date->format('l d F \à H\hi'),
                //     'subtitle'  => $group->name,
                //     'name'      => $event->place,
                //     'zip'       => $event->zipCode,
                //     'city'      => $event->city,
                //     'eventId'   => $event->id,
                //     'date'      => $date->format('l d F \à H\hi'),
                //     'day'       => $date->format('d'),
                //     'month'     => $date->format('F'),
                //     'ok'        => Model\Guest::RESP_OK,
                //     'no'        => Model\Guest::RESP_NO,
                //     'perhaps'   => Model\Guest::RESP_INCERTAIN,
                //     'comment'   => $data['comment'],
                //     'baseUrl'   => $config['baseUrl']
                // ]);
                // $mail->send();

                $this->flashMessenger()->addMessage('Votre évènement a bien été modifié.');
                $this->redirect()->toRoute('event', ['action' => 'detail', 'id' => $eventId]);
            }
        }

        return new ViewModel([
            'group'  => $group,
            'form'   => $form,
            'user'   => $this->getUser(),
        ]);
    }

    // public function deleteAction()
    // {
    //     $eventId       = $this->params('eventId');
    //     $eventMapper   = $this->_getMapper('event');
    //     $groupMapper   = $this->_getMapper('group');
    //     $guestMapper   = $this->_getMapper('guest');
    //     $commentMapper = $this->_getMapper('comment');
    //     $event         = $eventMapper->getById($eventId);

    //     $group = $this->_getMapper('group')->getById($event->groupId);

    //     if(!$group->isAdmin($this->user->id)) {
    //         $this->redirect()->toRoute('volley/not-found');
    //     }

    //     $comments = $commentMapper->fetchAll([
    //         'eventId' => $eventId
    //     ]);

    //     $guests = $guestMapper->fetchAll([
    //         'eventId' => $eventId
    //     ]);

    //     // delete comment
    //     foreach ($comments as $comment) {
    //         $commentMapper->delete($comment->id);
    //     }

    //     // delete guest
    //     foreach ($guests as $guest) {
    //         $guestMapper->delete($guest->id);
    //     }

    //     // delete event
    //     $eventMapper->delete($eventId);


    //     $this->flashMessenger()->addMessage('Évènement supprimé.');
    //     $this->redirect()->toRoute('volley/default');

    //     // Mail Event deleted
    //     // if (!$notifMapper->isAllowed(Notification::EVENT_UPDATE, $guest->userId)) continue;

    // }
}
