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

        $placeTable = $this->getContainer()->get(TableGateway\Place::class);
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $userTable  = $this->getContainer()->get(TableGateway\User::class);

        $group = $groupTable->find($groupId);
        $addresses = $placeTable->fetchAll(['groupId' => $groupId]);

        $places = [];
        foreach ($addresses as $address) {
            $places[$address->id] = $address->getFullAddress();
        }

        $form = new Form\Event();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post    = $request->getPost()->toArray();
            $placeId = $post['address'];
            $place   = $placeTable->find($placeId);

            $form->setData($request->getPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                $data['date']    = $date->format('Y-m-d H:i:s');
                $data['userId']  = $this->getUser()->id;
                $data['placeId'] = $placeId;
                $data['groupId'] = $groupId;

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
                        'date'     => $event->date
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
                    'name'      => $place->name,
                    'address'   => $place->address,
                    'zip'       => $place->zipCode,
                    'city'      => $place->city,
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

                $this->flashMessenger()->addMessage('Votre évènement a bien été créé.
                    Les notifications ont été envoyés aux membres du groupe.');
                $this->redirect()->toRoute('home');
            }
        }

        return new ViewModel([
            'group'  => $group,
            'form'   => $form,
            'user'   => $this->getUser(),
            'places' => $places
        ]);

    }

    public function detailAction()
    {
        $eventId      = $this->params()->fromRoute('id');
        $eventTable   = $this->getContainer()->get(TableGateway\Event::class);
        $placeTable   = $this->getContainer()->get(TableGateway\Place::class);
        $groupTable   = $this->getContainer()->get(TableGateway\Group::class);
        $guestTable   = $this->getContainer()->get(TableGateway\Guest::class);
        $userTable    = $this->getContainer()->get(TableGateway\User::class);
        $commentTable = $this->getContainer()->get(TableGateway\Comment::class);

        $form = new Form\Comment();

        $event     = $eventTable->find($eventId);
        $comments  = $commentTable->fetchAll($eventId);
        $group     = $groupTable->find($event->groupId);
        $place     = $placeTable->find($event->placeId);
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
            'counters' => $test,
            'comments' => $result,
            'event'    => $event,
            'place'    => $place,
            'form'     => $form,
            'group'    => $group,
            'users'    => $availability,
            'user'     => $this->getUser(),
            'date'     => $eventDate,
        ]);
    }

    // public function createAction()
    // {
    //     $inputErrors = array();
    //     $config      = $this->getServiceLocator()->get('volley-config');
    //     $eventMapper = $this->_getMapper('event');
    //     $placeMapper = $this->_getMapper('place');
    //     $groupMapper = $this->_getMapper('group');
    //     $placeMapper = $this->_getMapper('place');
    //     $guestMapper = $this->_getMapper('guest');
    //     $userMapper  = $this->_getMapper('user');
    //     $notifMapper = $this->_getMapper('notif');

    //     $groupId     = $this->params('groupId');
    //     $edit        = false;
    //     if ($eventId = $this->params('eventId', null)) {
    //         $edit = true;
    //         $event = $eventMapper->fetchOne([
    //             'id'      => $eventId,
    //             'groupId' => $groupId,
    //         ]);
    //     }

    //     $group = $groupMapper->getById($groupId);

    //     // Get all places
    //     $places = array(0 => 'Selectionne un groupe');
    //     $places = $placeMapper->fetchAll(['groupId' => $groupId]);

    //     $form = new CreateEvent();
    //     $checked = null;
    //     if (isset($event)) {
    //        $form->setData([
    //             'name'    => $event->name,
    //             'comment' => $event->comment,
    //             'date'    => $event->getDate()->format('d/m/Y H:i'),
    //             'places'  => $event->placeId,
    //         ]);
    //        $checked = $event->placeId;
    //     }

    //     $request = $this->getRequest();
    //     if ($request->isPost()) {
    //         $mail   = new Mail($this->getServiceLocator()->get('volley_transport_mail'));
    //         $formValidator = new CreateEventValidator($request->getPost()->toArray());
    //         $form->setInputFilter($formValidator->getInputFilter());
    //         $form->setData($request->getPost());
    //         $data = $request->getPost();
    //         if ($form->isValid()) {
    //             // Create Place
    //             $isCreation  = !empty($data['place-name']) || !empty($data['place-address']) || !empty($data['place-zipCode']) || !empty($data['place-city']);
    //             $isSelection = !empty($data['places']);
    //             if ($isSelection && !$isCreation) {
    //                 $place = $placeMapper->getById($data['places']);
    //             } else {
    //                 if (isset($data['places'])) unset($data['places']);
    //                 $placeData = array(
    //                     'address' => $data['place-address'],
    //                     'zipCode' => $data['place-zipCode'],
    //                     'city'    => $data['place-city'],
    //                     'name'    => $data['place-name'],
    //                 );

    //                 $maps = $this->getServiceLocator()->get('volley_googlemap_service');
    //                 $address = $data['place-address'] . ', ' . $data['place-zipCode'] . ' ' . $data['place-city'] . ' France';
    //                 if ($coords = $maps->getCoordinates($address)) {
    //                     $placeData = array_merge($placeData, $coords);
    //                 }
    //                 $place = $placeMapper->fromArray($placeData)->save();
    //             }

    //             if (empty($inputErrors)) {

    //                 // Create invitations
    //                 $userIds = array();
    //                 $users = json_decode($group->userIds, true);
    //                 if (!is_array($users)) $users = array($users);
    //                 foreach ($users as $id) {
    //                     if (in_array($id, $userIds)) continue;
    //                     $userIds[] = $id;
    //                 }

    //                 if (is_null($place->groupId)) {
    //                     $place->groupId = $group->id;
    //                     $placeMapper->setEntity($place)->save();
    //                 }

    //                 $dateTime = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);
    //                 $date = $dateTime->format('Y-m-d H:i:s');

    //                 // Create Event
    //                 $eventData = array(
    //                     'userId'  => $this->user->id,
    //                     'placeId' => $place->id,
    //                     'date'    => $date,
    //                     'name'    => $data['name'],
    //                     'comment' => $data['comment'],
    //                     'groupId' => $group->id,
    //                 );

    //                 if ($edit) {
    //                     $eventData['id'] = $event->id;
    //                     $template = Mail::TEMPLATE_EVENT_UPDATE;
    //                     $pitch = 'Évènement mis à jour!';
    //                     $notif = Notification::EVENT_UPDATE;
    //                 } else {
    //                     $template = Mail::TEMPLATE_EVENT;
    //                     $pitch = 'Nouvel évènement!';
    //                     $notif = Notification::EVENT_SIMPLE;
    //                 }

    //                 $event = $eventMapper->fromArray($eventData)->save();
    //                 $deviceMapper = $this->_getMapper('device');
    //                 $email = false;
    //                 foreach ($userIds as $id) {

    //                     if (!$notifMapper->isAllowed($notif, $id)) continue;

    //                     if ($deviceMapper->fetchOne(['userId' =>  $id, 'status' => Device::ACTIVE])) {
    //                         $pbUsers[] = $id;
    //                     } else {
    //                         $email = true;
    //                         $user = $userMapper->getById($id);
    //                         $mail->addBcc($user->email);
    //                     }
    //                     $params = array(
    //                         'eventId'  => $event->id,
    //                         'userId'   => $id,
    //                         'response' => Guest::RESP_NO_ANSWER,
    //                         'date'     => $date,
    //                         'groupId'  => $group->id,
    //                     );
    //                     $guest = $guestMapper->fromArray($params)->getEntity();
    //                     // make sure id is empty to avoid update. Create only here
    //                     $guest->id = null;
    //                     $guestMapper->setEntity($guest)->save();
    //                 }

    //                 $comment  = (empty($data['comment'])) ? 'Aucun commentaire sur l\'évènement' : nl2br($data['comment']);

    //                 if (!empty($pbUsers)) {
    //                     foreach ($pbUsers as $userId) {
    //                         $devices = $deviceMapper->fetchAll(['userId' => $userId, 'status' => Device::ACTIVE]);
    //                         $url = $config['baseUrl'] . '/event/detail/' . $event->id;
    //                         foreach ($devices as $device) {
    //                             $pb = new \Pushbullet\Pushbullet($device->token);
    //                             $pb->device($device->iden)->pushLink(
    //                                 'Évènement' . "\n" . $event->name . "\n" . $event->getDate()->format(),
    //                                 $url,
    //                                 $comment
    //                             );
    //                         }
    //                     }
    //                 }

    //                 // Send Email
    //                 if ($email) {   
    //                     $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $event->getDate()->format('l d F \à H\hi'));
    //                     $mail->setTemplate($template, array(
    //                         'pitch'     => $pitch,
    //                         'subtitle'  => $group->name,
    //                         'title'     => $event->name . ' <br /> ' . $event->getDate()->format('l d F \à H\hi'),
    //                         'name'      => $place->name,
    //                         'address'   => $place->address,
    //                         'zip'       => $place->zipCode,
    //                         'city'      => $place->city,
    //                         'eventId'   => $event->id,
    //                         'date'      => $event->getDate()->format('l d F \à H\hi'),
    //                         'day'       => $event->getDate()->format('d'),
    //                         'month'     => $event->getDate()->format('F'),
    //                         'ok'        => Guest::RESP_OK,
    //                         'no'        => Guest::RESP_NO,
    //                         'perhaps'   => Guest::RESP_INCERTAIN,
    //                         'comment'   => $comment,
    //                         'baseUrl'   => $config['baseUrl']
    //                     ));
    //                     $mail->send();
    //                 }

    //                 $this->flashMessenger()->addMessage('Évènement créé');
    //                 return $this->redirect()->toRoute('volley/event-detail', array(
    //                     'eventId' =>  $event->id,
    //                 ));
    //             }

    //         }
    //     }

    //     $this->layout()->group  = $group;
    //     $this->layout()->title  = $group->name;
    //     $this->layout()->subtitle  = 'CRÉER UN NOUVEL ÉVÈNEMENT';

    //     return new ViewModel(array(
    //         'checked'     => $checked,
    //         'places'      => $places,
    //         'inputErrors' => $form->getErrorElements(),
    //         'form'        => $form,
    //         'user'        => $this->user,
    //     ));
    // }

    // public function detailAction()
    // {
    //     $error           = false;
    //     $eventId         = $this->params('eventId');

    //     // get unread comments
    //     $badgeMapper = $this->_getMapper('badge');
    //     $badges = $badgeMapper->fetchAll([
    //         'userId'   => $this->user->id,
    //         'itemType' => Badge::TYPE_COMMENT,
    //         'itemId'   => $eventId,
    //     ]);

    //     $notifMapper = $this->_getMapper('notif');

    //     $eventMapper     = $this->_getMapper('event');
    //     $event           = $eventMapper->getById($eventId);

    //     $group           = $this->_getMapper('group')->getById($event->groupId);
    //     $this->_isAllowed($group);

    //     $placeMapper     = $this->_getMapper('place');
    //     $commentMapper   = $this->_getMapper('comment');
    //     $userMapper      = $this->_getMapper('user');
    //     $guestMapper     = $this->_getMapper('guest');
    //     $badgeMapper     = $this->_getMapper('badge');
    //     $place           = $placeMapper->getById($event->placeId);

    //     $result['place'] = $place;
    //     $result['event'] = $event;
    //     $form            = new CreateComment();
    //     $request         = $this->getRequest();
    //     $users           = array();

    //     $admin           = $group->isAdmin($this->user->id);
    //     $result['group'] = $group;
    //     $hasWeather      = $group->weather && $event->getDate()->format('U') > time() && $event->getDate()->format('U') < strtotime('+ 7 days');

    //     $badges = $badgeMapper->fetchAll([
    //         'userId'   => $this->user->id,
    //         'itemType' => Badge::TYPE_COMMENT,
    //         'itemId'   => $eventId,
    //     ]);

    //     $userResponse = $guests = $guestMapper->fetchOne(array(
    //         'eventId' => $eventId,
    //         'userId'  => $this->user->id
    //     ));

    //     if ($request->isPost()) {
    //         $formValidator = new CreateCommentValidator();
    //         $form->setInputFilter($formValidator->getInputFilter());
    //         $form->setData($request->getPost());

    //         if ($form->isValid()) {
    //             $config = $this->getServiceLocator()->get('volley-config');
    //             $data   = $form->getData();
    //             $data['userId']  = $this->user->id;
    //             $data['date']    = date('Y-m-d H:i:s');
    //             $data['eventId'] = $eventId;
    //             unset($data['submit']);
    //             $comment = $commentMapper->fromArray($data)->save();
    //             foreach (json_decode($group->userIds, true) as $userId) {
    //                 if ($userId == $this->user->id) continue;
    //                 $badge = $badgeMapper->fromArray([
    //                     'userId'   => $userId,
    //                     'itemType' => Badge::TYPE_COMMENT,
    //                     'itemId'   => $event->id,
    //                 ])->save();
    //                 $badge->id = null;
    //             }

    //             $mail = new Mail($this->getServiceLocator()->get('volley_transport_mail'));
    //             $guests = $guestMapper->fetchAll(array(
    //                 'eventId' => $eventId
    //             ));
    //             $bbc     = [];
    //             $pbUsers = [];
    //             $deviceMapper = $this->_getMapper('device');
    //             $notifMapper = $this->_getMapper('notif');
    //             foreach ($guests as $guest) {

    //                 if (!$notifMapper->isAllowed(Notification::COMMENTS, $guest->userId)) continue;
    //                 if (!$notifMapper->isAllowed(Notification::SELF_COMMENT, $guest->userId)) continue;
    //                 if ($guest->response == Guest::RESP_NO && !$notifMapper->isAllowed(Notification::COMMENT_ABSENT, $guest->userId)) continue;

    //                 if ($pushbullet = $deviceMapper->fetchOne(['userId' => $guest->userId, 'status' => Device::ACTIVE])) {
    //                     $pbUsers[] = $guest->userId;
    //                 } else {
    //                     if (!isset($users[$guest->userId])) {
    //                         $users[$guest->userId] = $userMapper->getById($guest->userId);
    //                     }
    //                     $bcc[] = $users[$guest->userId]->email;
    //                 }
    //             }
    //             if (!empty($bcc)) {
    //                 $mail->addBcc($bcc);
    //                 $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $event->getDate()->format('l d F \à H\hi'));
    //                 $mail->setTemplate(Mail::TEMPLATE_COMMENT, array(
    //                     'title'     => $event->name . '<br>' . $event->getDate()->format('l d F \à H\hi'),
    //                     'subtitle'  => $group->name,
    //                     'username'  => $this->user->getFullname(),
    //                     'comment'   => nl2br($comment->comment),
    //                     'date'       => $comment->getDate()->format('d\/m'),
    //                     'eventId'   => $eventId,
    //                     'baseUrl'   => $config['baseUrl']

    //                 ));
    //                 $mail->send();
    //             }

    //             if (!empty($pbUsers)) {
    //                 foreach ($pbUsers as $userId) {
    //                     $devices = $deviceMapper->fetchAll(['userId' => $userId, 'status' => Device::ACTIVE]);
    //                     $url = $config['baseUrl'] . '/event/detail/' . $eventId . '#comment';
    //                     foreach ($devices as $device) {
    //                         $pb = new \Pushbullet\Pushbullet($device->token);
    //                         $pb->device($device->iden)->pushLink(
    //                             'Commentaire' . "\n" . 
    //                             $event->name . ' ' . $event->getDate()->format('d/m') . "\n" . 
    //                             $this->user->getFullname() . "\n",
    //                             $url,
    //                             $comment->comment
    //                         );
    //                     }
    //                 }
    //             }

    //             $this->flashMessenger()->addMessage('Commentaire enregistré');

    //         } else {
    //             $error = true;
    //         }
    //     }

    //     foreach (array(Guest::RESP_OK, Guest::RESP_NO, Guest::RESP_INCERTAIN, Guest::RESP_NO_ANSWER) as $response) {
    //         $counts[$response] = array();
    //         $guests = $guestMapper->fetchAll(array('eventId' => $eventId, 'response' => $response));
    //         foreach ($guests as $guest) {
    //             if (!isset($users[$guest->userId])) {
    //                 $users[$guest->userId] = $userMapper->getById($guest->userId);
    //             }
    //             $counts[$response][$guest->userId] = $users[$guest->userId]->getFullname();
    //         }
    //     }

    //     $comments = $commentMapper->fetchAll(array('eventId' => $event->id));
    //     $count    = count($comments);
    //     $commentsRes = array();
    //     foreach ($comments as $comment) {
    //         if (!isset($users[$comment->userId])) {
    //             $users[$comment->userId] = $userMapper->getById($comment->userId);
    //         }
    //         $commentsRes[$comment->id]['user'] = $users[$comment->userId]->getfullname();
    //         $commentsRes[$comment->id]['date'] = $comment->getDate();
    //         $commentsRes[$comment->id]['comment'] = $comment->comment;
    //     }

    //     $this->layout()->title = $event->name;
    //     $this->layout()->subtitle = $event->getDate()->format('l d F \à H\hi');

    //     return new ViewModel(array(
    //         'hasMeteo'     => $hasWeather,
    //         'form'         => $form,
    //         'comments'     => $commentsRes,
    //         'error'        => $error,
    //         'event'        => $result,
    //         'user'         => $this->user,
    //         'counts'       => $counts,
    //         'admin'        => $admin,
    //         'group'        => $group,
    //         'userResponse' => $userResponse->response,
    //         'count'        => $count,
    //         'badges'       => $badges
    //     ));
    // }

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
