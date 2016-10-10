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
            $form = new Form\Event();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post  = $request->getPost();
                $match = $post->match == 'on';
                unset($post->match);

                $form->setData($post);
                if ($form->isValid()) {

                    $data = $form->getData();
                    $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                    $data['date']    = $date->format('Y-m-d H:i:s');
                    $data['groupId'] = $groupId;

                    $mapService = $this->get(Service\Map::class);
                    $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                    if ($coords = $mapService->getCoordinates($address)) {
                        $data = array_merge($data, $coords);
                    }

                    $event = new Model\Event();
                    $event->exchangeArray($data);
                    $event->id = $eventTable->save($event);

                    if ($match) {
                        $matchTable = $this->get(TableGateway\Match::class);
                        $match = new Model\Match;
                        $match->exchangeArray([
                            'eventId' => $event->id
                        ]);
                        $matchTable->save($match);
                    }

                    // Create guest for this new event
                    $emails = [];
                    $userGroups = $userGroupTable->fetchAll(['groupId' => $group->id]);

                    foreach ($userGroups as $userGroup) {
                        $user     = $userTable->find($userGroup->userId);
                        $emails[] = $user->email;

                        $guest = new Model\Guest();
                        $guest->exchangeArray([
                            'eventId'  => $event->id,
                            'userId'   => $userGroup->userId,
                            'response' => Model\Guest::RESP_NO_ANSWER,
                            'groupId'  => $groupId,
                        ]);

                        $guestTable->save($guest);
                        unset($guest);
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
                        $mail->send();
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
                $bcc[] = $users[$guest->userId]->email;
            }

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

                    $config = $this->get('config');
                    if ($config['mail']['allowed']) {
                        $commentDate = \DateTime::createFromFormat('U', time());
                        $mail   = $this->get(MailService::class);
                        $mail->addBcc($bcc);
                        $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $eventDate->format('l d F \à H\hi'));
                        $mail->setTemplate(MailService::TEMPLATE_COMMENT, array(
                            'title'     => $event->name . '<br>' . $eventDate->format('l d F \à H\hi'),
                            'subtitle'  => $group->name,
                            'username'  => $this->getUser()->getFullname(),
                            'comment'   => nl2br($comment->comment),
                            'date'      => $commentDate->format('d\/m'),
                            'eventId'   => $eventId,
                            'baseUrl'   => $config['baseUrl']

                        ));
                        $mail->send();
                    }

                    $this->flashMessenger()->addMessage('Votre commentaire a bien été enregistré.');
                    $this->redirect()->toRoute('event', ['action' => detail, 'id' => $eventId]);
                }
            }

            $result = [];
            foreach ($comments as $comment) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $comment->date);
                $result[$comment->id]['date']    = $date->format('d F Y \à H:i');
                $result[$comment->id]['author']  = $users[$comment->userId];
                $result[$comment->id]['comment'] = $comment->comment;
            }

             $test = $guestTable->getCounters($eventId);

            $config     = $this->get('config');
            $baseUrl    = $config['baseUrl'];

            $this->layout()->opacity = true;
            $this->layout()->user = $this->getUser();
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
            $form->setData($event->toArray());
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post    = $request->getPost()->toArray();

                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $data = $form->getData();
                    $date = \DateTime::createFromFormat('d/m/Y H:i', $data['date']);

                    $data['date']    = $date->format('Y-m-d H:i:s');
                    $mapService = $this->get(Service\Map::class);
                    $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                    if ($coords = $mapService->getCoordinates($address)) {
                        $data = array_merge($data, $coords);
                    }

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
