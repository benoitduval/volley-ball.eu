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
                        $view       = new \Zend\View\Renderer\PhpRenderer();
                        $resolver   = new \Zend\View\Resolver\TemplateMapResolver();
                        $resolver->setMap([
                            'event' => __DIR__ . '/../../view/mail/event.phtml'
                        ]);
                        $view->setResolver($resolver);

                        $viewModel  = new ViewModel();
                        $viewModel->setTemplate('event')->setVariables(array(
                            'event'     => $event,
                            'group'     => $group,
                            'date'      => $date,
                            'baseUrl'   => $config['baseUrl']
                        ));

                        $mail = $this->get(MailService::class);
                        $mail->addBcc($emails);
                        $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . \Application\Service\Date::toFr($date->format('l d F \à H\hi')));
                        $mail->setBody($view->render($viewModel));
                        try {
                            $mail->send();
                        } catch (\Exception $e) {
                        }
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
        $eventId = $this->params('id');
        $stats   = [];
        $sets    = [];
        if (($event = $this->eventTable->find($eventId)) && $this->userGroupTable->isMember($this->getUser()->id, $event->groupId)) {

            for ($i = 1; $i <= 5; $i++) {
                $stats[$i] = $this->statsTable->fetchAll(['eventId' => $eventId, 'set' => $i], 'id ASC');
                foreach ($stats[$i] as $stat) {
                    $sets[$i]['us'][] = ($stat->pointFor == Model\Stats::POINT_US) ? $stat->scoreUs: '-';
                    $sets[$i]['them'][] = ($stat->pointFor == Model\Stats::POINT_THEM) ? $stat->scoreThem: '-';
                }
            }

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

                        $config = $this->get('config');
                        if ($config['mail']['allowed']) {
                                $commentDate = \DateTime::createFromFormat('U', time());
                                $date        = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);
                                $view       = new \Zend\View\Renderer\PhpRenderer();
                                $resolver   = new \Zend\View\Resolver\TemplateMapResolver();
                                $resolver->setMap([
                                    'event' => __DIR__ . '/../../view/mail/comment.phtml'
                                ]);
                                $view->setResolver($resolver);

                                $viewModel  = new ViewModel();
                                $viewModel->setTemplate('event')->setVariables(array(
                                    'event'     => $event,
                                    'group'     => $group,
                                    'date'      => $date,
                                    'user'      => $this->getUser(),
                                    'comment'   => $comment,
                                    'commentDate' => $commentDate,
                                    'baseUrl'   => $config['baseUrl']
                                ));

                                $mail = $this->get(MailService::class);
                                $mail->addBcc($bcc);
                                $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . \Application\Service\Date::toFr($date->format('l d F \à H\hi')));
                                $mail->setBody($view->render($viewModel));
                            try {
                                $mail->send();
                            } catch (\Exception $e) {
                            }
                        }
                    }
                    $this->flashMessenger()->addSuccessMessage('Commentaire enregistré');
                    $this->redirect()->toUrl('/event/detail/' . $event->id);
                }
            }

            return new ViewModel([
                'stats'           => $stats,
                'sets'            => $sets,
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

            $stats = $this->statsTable->fetchOne(['eventId' => $eventId], 'id DESC');
            $scoreUs   = 0;
            $scoreThem = 0;
            $set       = 1;
            if ($stats) {
                if (($stats->scoreUs >= 25 || $stats->scoreThem >= 25) && (abs(
                $stats->scoreThem - $stats->scoreUs) == 2)) {
                    $set = $stats->set + 1;
                } else {
                    $set = $stats->set;
                    $scoreUs   = $stats->scoreUs;
                    $scoreThem = $stats->scoreThem;
                }
            }

            $form = new Form\Result;

            $request = $this->getRequest();
            if ($request->isPost()) {
                $result = [];
                $post = $request->getPost()->toArray();
                if ($post['point-for'] == Model\Stats::POINT_US) {
                    $post['score-us']++;
                } else {
                    $post['score-them']++;
                }
                $data['scoreUs']     = $post['score-us'];
                $data['scoreThem']   = $post['score-them'];
                $data['pointFor']    = $post['point-for'];
                $data['reason']      = $post['reason'];
                $data['eventId']     = $eventId;
                $data['set']         = $post['set'];
                $data['duringPoint'] = json_encode([
                    'us'   => isset($post['during-point-us']) ? $post['during-point-us'] : null,
                    'them' => isset($post['during-point-them']) ? $post['during-point-them'] : null,
                ]);
                $stats = $this->statsTable->save($data);

                $this->flashMessenger()->addSuccessMessage('Votre match a bien été enregistré.');
                $this->redirect()->toRoute('event', ['action' => 'match', 'id' => $eventId]);
            }

            return new ViewModel([
                'event'     => $event,
                'form'      => $form,
                'user'      => $this->getUser(),
                'scoreUs'   => $scoreUs,
                'scoreThem' => $scoreThem,
                'set'       => (int) $set,
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}
