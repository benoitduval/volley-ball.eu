<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\TableGateway;

class GuestController extends AbstractController
{
    public function responseAction()
    {
        $eventId    = $this->params()->fromRoute('id');
        $responseId = $this->params()->fromRoute('response');

        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $groupTable = $this->getContainer()->get(TableGateway\Event::class);

        $event      = $eventTable->find($eventId);
        $guest      = $guestTable->fetchOne([
            'eventId' => $eventId,
            'userId'  => $this->getUser()->id,
        ]);
        $group = $groupTable->find($event->groupId);
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);

        if ($date->format('Ymd') >= date('Ymd')) {
            if ($guest->response != $responseId) {
                $guest->response = $responseId;
                $guestTable->save($guest);
            }
            $this->flashMessenger()->addMessage('Votre réponse a été prise en compte.');
        } else {    
            $this->flashMessenger()->addErrorMessage('Impossible de modifier un événement passé');
        }

        $this->redirect()->toRoute('event', ['id' => $eventId]);
    }
}