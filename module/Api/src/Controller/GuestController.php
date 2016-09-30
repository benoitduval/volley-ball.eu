<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;

class GuestController extends AbstractController
{

    public function responseAction()
    {
        $eventId  = $this->params('eventId', null);
        $response = $this->params('response', null);

        $guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        if ($guest = $guestTable->fetchOne(['userId' => $this->getUser()->id, 'eventId' => $eventId])) {
            $guest->response = $response;
            $guestTable->save($guest);
        }

        $counts = $guestTable->getCounters($eventId);

        $view = new ViewModel(array(
            'result'   => [
                'success'  => true,
                'counts' => $counts
            ]
        ));

        $view->setTerminal(true);
        $view->setTemplate('api/default/json.phtml');
        return $view;
    }
}