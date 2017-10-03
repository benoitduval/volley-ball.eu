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

        $isAdmin = $this->userGroupTable->isAdmin($this->getUser()->id, $groupId);
        if (($group = $this->groupTable->find($groupId)) && $isAdmin) {
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

                    $event = $this->eventTable->save($data);

                    if ($match) {
                        $this->matchTable->save([
                            'eventId' => $event->id
                        ]);
                    }

                    // Create disponibility for this new event
                    $emails = [];
                    // $userGroups = $this->userGroupTable->fetchAll(['groupId' => $group->id]);
                    $users = $this->userTable->getAllByGroupId($groupId);
                    foreach ($users as $user) {
                        $absent = $this->holidayTable->fetchOne([
                            '`from` < ?' => $date->format('Y-m-d H:i:s'),
                            '`to` > ?'   => $date->format('Y-m-d H:i:s'),
                            'userId = ?' => $user->id
                        ]);

                        if ($absent) {
                            $response = Model\Disponibility::RESP_NO;
                        } else {
                            $response = Model\Disponibility::RESP_NO_ANSWER;
                            if ($this->notifTable->isAllowed(Model\Notification::EVENT_SIMPLE, $user->id)) {
                                $emails[] = $user->email;
                            }
                        }

                        $this->disponibilityTable->save([
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
                        $emailData = [
                            'title'     => $event->name . ' <br /> ' . $date->format('l d F \à H\hi'),
                            'subtitle'  => $group->name,
                            'name'      => $event->place,
                            'address'   => $event->address,
                            'zip'       => $event->zipCode,
                            'city'      => $event->city,
                            'eventId'   => $event->id,
                            'date'      => $date->format('l d F \à H\hi'),
                            'day'       => $date->format('d'),
                            'month'     => $date->format('F'),
                            'eventDate' => $event->date,
                            'ok'        => Model\Disponibility::RESP_OK,
                            'no'        => Model\Disponibility::RESP_NO,
                            'perhaps'   => Model\Disponibility::RESP_INCERTAIN,
                            'comment'   => $data['comment'],
                            'baseUrl'   => $config['baseUrl']
                        ];

                        // $mail->addIcalEvent($event);
                        $mail->setTemplate(MailService::TEMPLATE_EVENT, $emailData);
                        try {
                            $mail->send();
                        } catch (\Exception $e) {}
                    }

                    $this->flashMessenger()->addSuccessMessage('Votre évènement a bien été créé. Les notifications ont été envoyés aux membres du groupe.');
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

        if (($event = $this->eventTable->find($eventId)) && $this->userGroupTable->isMember($this->getUser()->id, $event->groupId)) {

            $comments  = $this->commentTable->fetchAll(['eventId' => $event->id]);
            $group     = $this->groupTable->find($event->groupId);
            $isMember  = $this->userGroupTable->isAdmin($this->getUser()->id, $group->id);
            $isAdmin   = false;
            $serve  = '';
            $attack = '';
            $recep  = '';
            if ($isMember) {
                $isAdmin = $this->userGroupTable->isAdmin($this->getUser()->id, $group->id);
            }

            $counters  = $this->disponibilityTable->getCounters($eventId);
            $disponibilities    = $this->disponibilityTable->fetchAll(['eventId' => $eventId]);
            $myGuest   = $this->disponibilityTable->fetchOne(['eventId' => $eventId, 'userId' => $this->getUser()->id]);
            $eventDate = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);

            $availability    = [
                Model\Disponibility::RESP_NO_ANSWER => [],
                Model\Disponibility::RESP_OK        => [],
                Model\Disponibility::RESP_NO        => [],
                Model\Disponibility::RESP_INCERTAIN => [],
            ];

            foreach ($disponibilities as $disponibility) {
                $users[$disponibility->userId] = $this->userTable->find($disponibility->userId);
                $availability[$disponibility->response][] = $users[$disponibility->userId];
            }

            $result = [];
            foreach ($comments as $comment) {
                if (!isset($users[$comment->userId])) {
                    $author = $this->userTable->find($comment->userId);
                } else {
                    $author = $users[$comment->userId];
                }

                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $comment->date);
                $result[$comment->id]['date']    = $date->format('d F Y \à H:i');
                $result[$comment->id]['author']  = $author;
                $result[$comment->id]['comment'] = $comment->comment;
            }

            $counters = $this->disponibilityTable->getCounters($eventId);

            if ($event->stats) {
                $stats = $event->getStatsByType();
                $serve  = [$stats[\Application\Model\Event::STAT_SERVE_FAULT], $stats[\Application\Model\Event::STAT_SERVE_POINT]];
                $attack = [$stats[\Application\Model\Event::STAT_ATTACK_FAULT], $stats[\Application\Model\Event::STAT_ATTACK_POINT]];
                $recep  = [$stats[\Application\Model\Event::STAT_RECEP_FAULT]];
            }

            $config     = $this->get('config');
            $baseUrl    = $config['baseUrl'];

            // User submit commment
            $form = new Form\Comment();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $eventDate   = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);
                    $comment = $this->commentTable->save([
                        'date'    => date('Y-m-d H:i:s'),
                        'eventId' => $eventId,
                        'userId'  => $this->getUser()->id,
                        'comment' => $data['comment'],
                    ]);

                    $config = $this->get('config');
                    if ($config['mail']['allowed']) {
                        $users = $this->userTable->getAllByGroupId($group->id);
                        $bcc   = [];
                        foreach ($users as $user) {
                            $email = true;
                            $disponibility = $this->disponibilityTable->fetchOne(['userId' => $user->id, 'eventId' => $event->id]);
                            if ($disponibility && $disponibility->response = Model\Disponibility::RESP_NO && !$this->notifTable->isAllowed(Model\Notification::COMMENT_ABSENT, $user->id)) {
                                $email = false;
                            } else if ($this->getUser()->id == $user->id && !$this->notifTable->isAllowed(Model\Notification::SELF_COMMENT, $user->id)) {
                                $email = false;
                            } else if (!$this->notifTable->isAllowed(Model\Notification::COMMENTS, $user->id)) {
                                $email = false;
                            }

                            if ($email) $bcc[] = $user->email;
                        }

                        $commentDate = \DateTime::createFromFormat('U', time());
                        $mail        = $this->get(MailService::class);
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
                    $this->flashMessenger()->addSuccessMessage('Commentaire enregistré');
                    $this->redirect()->toUrl('/event/detail/' . $event->id);
                }
            }

            return new ViewModel([
                'serve'           => $serve,
                'attack'          => $attack,
                'recep'           => $recep,
                'counters'        => $counters,
                'comments'        => $result,
                'event'           => $event,
                'form'            => $form,
                'group'           => $group,
                'users'           => $availability,
                'user'            => $this->getUser(),
                'date'            => $eventDate,
                'isAdmin'         => $isAdmin,
                'isMember'        => $isMember,
                'myDisponibility' => $myGuest,
                'disponibilities' => json_encode(array_values($counters))
            ]);
        } else {
            $this->redirect()->toRoute('home');
        }
    }

    public function editAction()
    {
        $eventId    = $this->params()->fromRoute('id');
        if (($event = $this->eventTable->find($eventId)) && $this->userGroupTable->isAdmin($this->getUser()->id, $event->groupId)) {

            $event = $this->eventTable->find($eventId);
            $group = $this->groupTable->find($event->groupId);

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
                    $this->eventTable->save($event);

                    $this->flashMessenger()->addSuccessMessage('Votre évènement a bien été modifié.');
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

    public function deleteAction()
    {
        $eventId = $this->params()->fromRoute('id');

        if (($event = $this->eventTable->find($eventId)) && $this->userGroupTable->isAdmin($this->getUser()->id, $event->groupId)) {
            $this->commentTable->delete(['eventId' => $event->id]);
            $this->disponibilityTable->delete(['eventId' => $event->id]);
            $this->eventTable->delete(['id' => $event->id]);
            $group = $this->groupTable->find($event->groupId);
            $this->redirect()->toUrl('/welcome-to/' . $group->brand);
        }
    }

    public function matchAction()
    {
        $eventId = $this->params('id');
        if (($event = $this->eventTable->find($eventId)) && $this->userGroupTable->isAdmin($this->getUser()->id, $event->groupId)) {

            $eventData = [];
            $stats = json_decode($event->stats);
            foreach ($event->sets as $key => $score) {
                $i = $key + 1;
                $set = explode('-', $score);
                $eventData['set' . $i . 'Team1'] = $set[0];
                $eventData['set' . $i . 'Team2'] = $set[1];
                $eventData['set' . $i . 'ServeFault']  = $stats[$key][Model\Event::STAT_SERVE_FAULT];
                $eventData['set' . $i . 'RecepFault']  = $stats[$key][Model\Event::STAT_RECEP_FAULT];
                $eventData['set' . $i . 'AttackFault'] = $stats[$key][Model\Event::STAT_ATTACK_FAULT];
                $eventData['set' . $i . 'ServePoint']  = $stats[$key][Model\Event::STAT_SERVE_POINT];
                $eventData['set' . $i . 'AttackPoint']  = $stats[$key][Model\Event::STAT_ATTACK_POINT];
            }
            $eventData['debrief'] = $event->debrief;

            $form = new Form\Result;

            $form->setData($eventData);
            $request = $this->getRequest();
            if ($request->isPost()) {
                $result = [];
                $post = $request->getPost();
                $setFor     = 0;
                $setAgainst = 0;
                $sets  = [];
                $stats = [];
                for ($i = 1; $i <= 5; $i++) {
                    if ($post['set'.$i.'Team1'] && $post['set'.$i.'Team2']) {
                        if ($post['set'.$i.'Team1'] > $post['set'.$i.'Team2']) {
                            $setFor++;
                        } else {
                            $setAgainst++;
                        }
                        $sets[]  = $post['set'.$i.'Team1'] . '-' . $post['set'.$i.'Team2'];
                        $stats[] = [
                            (int) $post['set' . $i . 'ServeFault'],
                            (int) $post['set' . $i . 'RecepFault'],
                            (int) $post['set' . $i . 'AttackFault'],
                            (int) $post['set' . $i . 'ServePoint'],
                            (int) $post['set' . $i . 'AttackPoint'],
                       ];
                    }
                }
                $result['sets']    = json_encode($sets);
                $result['stats']   = json_encode($stats);
                $result['victory'] = ($setFor > $setAgainst) ? 1 : 0;
                $result['score']   = $setFor . ' / ' .  $setAgainst;
                $result['debrief'] = $post['debrief'];
                $event->exchangeArray($result);
                $this->eventTable->save($event);

                $this->flashMessenger()->addSuccessMessage('Votre match a bien été enregistré.');
                $this->redirect()->toRoute('event', ['action' => 'detail', 'id' => $eventId]);
            }

            return new ViewModel([
                'event'  => $event,
                'form'   => $form,
                'user'   => $this->getUser(),
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}
