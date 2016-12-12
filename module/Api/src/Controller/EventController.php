<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;

class EventController extends AbstractController
{

    public function exportAction()
    {
        if ($this->getUser()) {
            $events = $this->get(TableGateway\Event::class)->getActiveByUserId($this->getUser()->id);
            $calendar = new \Application\Service\Calendar($events->toArray());
            $ical = $calendar->generateICS();

            $view = new ViewModel(['ical' => $ical]);
            $view->setTerminal(true);
            $view->setTemplate('api/default/data.phtml');
            return $view;
        }
    }
}