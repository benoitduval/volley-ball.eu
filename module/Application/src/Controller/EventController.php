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
        $groupId        = $this->params('id', null);
        $groupTable     = $this->get(TableGateway\Group::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);

        $isAdmin = $userGroupTable->isAdmin($this->getUser()->id, $groupId);
        if (($group = $groupTable->find($groupId)) && $isAdmin) {
            $eventTable     = $this->get(TableGateway\Event::class);
            $guestTable     = $this->get(TableGateway\Guest::class);
            $userTable      = $this->get(TableGateway\User::class);
            $absentTable    = $this->get(TableGateway\Absent::class);
            $notifTable     = $this->get(TableGateway\Notification::class);
            $form = new Form\Event();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post  = $request->getPost();
                $match = isset($post->match);
                if ($match) unset($post->match);

                $form->setData($post);
                if ($form->isValid()) {

                    $data = $form->getData();
                    $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                    $data['date']    = $date->format('Y-m-d H:i:s');
                    $data['groupId'] = $groupId;

                    try {
                        $mapService = $this->get(Service\Map::class);
                        $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                        if ($coords = $mapService->getCoordinates($address)) {
                            $data = array_merge($data, $coords);
                        }
                    } catch (\Exception $e) {}

                    $event = $eventTable->save($data);

                    if ($match) {
                        $matchTable = $this->get(TableGateway\Match::class);
                        $matchTable->save([
                            'eventId' => $event->id
                        ]);
                    }

                    // Create guest for this new event
                    $emails = [];
                    // $userGroups = $userGroupTable->fetchAll(['groupId' => $group->id]);
                    $users = $userTable->getGroupUsers($groupId);
                    foreach ($users as $user) {
                        $absent = $absentTable->fetchOne([
                            '`from` < ?' => $date->format('Y-m-d H:i:s'),
                            '`to` > ?'   => $date->format('Y-m-d H:i:s'),
                            'userId = ?' => $user->id
                        ]);

                        if ($absent) {
                            $response = Model\Guest::RESP_NO;
                        } else {
                            $response = Model\Guest::RESP_NO_ANSWER;
                            if ($notifTable->isAllowed(Model\Notification::EVENT_SIMPLE, $user->id)) {
                                $emails[] = $user->email;
                            }
                        }

                        $guestTable->save([
                            'eventId'  => $event->id,
                            'userId'   => $user->id,
                            'response' => $response,
                            'groupId'  => $groupId,
                        ]);
                    }

                    // send emails
                    $config = $this->get('config');
                    if ($config['mail']['allowed']) {
                        $mail   = $this->get(MailService::class);
                        $mail->addBcc($emails);
                        $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $date->format('l d F \à H\hi'));

                        $mail->setTemplate(MailService::TEMPLATE_EVENT, [
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
                        try {
                            $mail->send();
                        } catch (\Exception $e) {}
                    }

                    $this->flashMessenger()->addMessage('Votre évènement a bien été créé.
                        Les notifications ont été envoyés aux membres du groupe.');
                    $this->redirect()->toRoute('home');
                }
            }

            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'group'   => $group,
                'form'    => $form,
                'user'    => $this->getUser(),
                'isAdmin' => $isAdmin
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function detailAction()
    {
        $eventId        = $this->params('id');
        $eventTable     = $this->get(TableGateway\Event::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);

        if (($event = $eventTable->find($eventId)) && $userGroupTable->isMember($this->getUser()->id, $event->groupId)) {
            $groupTable     = $this->get(TableGateway\Group::class);
            $guestTable     = $this->get(TableGateway\Guest::class);
            $userTable      = $this->get(TableGateway\User::class);
            $commentTable   = $this->get(TableGateway\Comment::class);
            $matchTable     = $this->get(TableGateway\Match::class);
            $notifTable     = $this->get(TableGateway\Notification::class);

            $form = new Form\Comment();

            $match     = $matchTable->fetchOne(['eventId' => $event->id]);
            $comments  = $commentTable->fetchAll(['eventId' => $event->id]);
            $group     = $groupTable->find($event->groupId);
            $isMember  = $userGroupTable->isAdmin($this->getUser()->id, $group->id);
            $isAdmin   = false;
            if ($isMember) {
                $isAdmin = $userGroupTable->isAdmin($this->getUser()->id, $group->id);
            }

            $counters  = $guestTable->getCounters($eventId);
            $guests    = $guestTable->fetchAll(['eventId' => $eventId]);
            $eventDate = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);

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
                $result[$comment->id]['date']    = $date->format('d F Y \à H:i');
                $result[$comment->id]['author']  = $users[$comment->userId];
                $result[$comment->id]['comment'] = $comment->comment;
            }

            $counters = $guestTable->getCounters($eventId);

            $config     = $this->get('config');
            $baseUrl    = $config['baseUrl'];

            $this->layout()->opacity = true;
            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'match'    => $match,
                'counters' => $counters,
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
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function editAction()
    {
        $eventId    = $this->params()->fromRoute('id');
        $eventTable = $this->get(TableGateway\Event::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);
        if (($event = $eventTable->find($eventId)) && $userGroupTable->isMember($this->getUser()->id, $event->groupId)) {

            $groupTable = $this->get(TableGateway\Group::class);
            $event = $eventTable->find($eventId);
            $group = $groupTable->find($event->groupId);

            $form = new Form\Event();
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);
            $formData = $event->toArray();
            $formData['date'] = $date->format('d/m/Y H:i');
            $form->setData($formData);
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post    = $request->getPost()->toArray();

                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $data = $form->getData();
                    $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                    $data['date']    = $date->format('Y-m-d H:i:s');

                    $event->exchangeArray($data);
                    $eventTable->save($event);

                    $this->flashMessenger()->addMessage('Votre évènement a bien été modifié.');
                    $this->redirect()->toRoute('event', ['action' => 'detail', 'id' => $eventId]);
                }
            }

            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'group'  => $group,
                'form'   => $form,
                'user'   => $this->getUser(),
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}
