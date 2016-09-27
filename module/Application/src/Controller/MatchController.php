<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;
use Application\Service\MailService;

class MatchController extends AbstractController
{
    public function createAction()
    {
        $eventId    = $this->params()->fromRoute('id');
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $event      = $eventTable->find($eventId);

        $matchTable = $this->getContainer()->get(TableGateway\Match::class);

        $form = new Form\Match;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post  = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {

                $data = $form->getData();
                $setFor     = 0;
                $setAgainst = 0;
                for ($i = 1; $i <= 5; $i++) {
                    if ($data['set'.$i.'Team1'] && $data['set'.$i.'Team2']) {
                        if ($data['set'.$i.'Team1'] > $data['set'.$i.'Team2']) {
                            $setFor++;
                        } else {
                            $setAgainst++;
                        }
                    }

                    $data['victory'] = ($setFor > $setAgainst) ? 1 : 0;
                    $data['sets']    = $setFor . ' / ' .  $setAgainst;
                    $data['eventId'] = $eventId;
                }

                $match = new Model\Match;
                $match->exchangeArray($data);
                $matchTable->save($match);

                $this->flashMessenger()->addMessage('Votre match a bien été enregistré.');
                $this->redirect()->toRoute('event', ['action' => detail, 'id' => $eventId]);
            }
        }

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'event'  => $event,
            'form'   => $form,
            'user'   => $this->getUser(),
        ]);
    }

    public function editAction()
    {
        $matchId    = $this->params()->fromRoute('id');
        $matchTable = $this->getContainer()->get(TableGateway\Match::class);
        $match = $matchTable->find($matchId);

        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $event      = $eventTable->find($match->eventId);

        $form = new Form\Match;
        $request = $this->getRequest();
        $form->setData($match->toArray());
        if ($request->isPost()) {
            $post  = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {

                $data = $form->getData();
                $setFor     = 0;
                $setAgainst = 0;
                for ($i = 1; $i <= 5; $i++) {
                    if ($data['set'.$i.'Team1'] && $data['set'.$i.'Team2']) {
                        if ($data['set'.$i.'Team1'] > $data['set'.$i.'Team2']) {
                            $setFor++;
                        } else {
                            $setAgainst++;
                        }
                    }

                    $data['victory'] = ($setFor > $setAgainst) ? 1 : 0;
                    $data['sets']    = $setFor . ' / ' .  $setAgainst;
                    $data['eventId'] = $event->id;
                }
                $data['id'] = $match->id;
                $match->exchangeArray($data);
                $matchTable->save($match);
            }
            $this->flashMessenger()->addMessage('Votre match a bien été enregistré.');
            $this->redirect()->toRoute('event', ['action' => detail, 'id' => $event->id]);
        }

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'event'  => $event,
            'form'   => $form,
            'user'   => $this->getUser(),
        ]);
    }
}