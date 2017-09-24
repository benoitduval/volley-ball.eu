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

    public function getAction($value='')
    {
        if ($this->getUser()) {
            $events = $this->get(TableGateway\Event::class)->getAllByUserId($this->getUser()->id);
            $guestTable = $this->get(TableGateway\Guest::class);
            $result = [];
            $config = $this->get('config');
            foreach ($events as $event) {
                $match = $this->matchTable->fetchOne(['eventId' => $event->id, 'set1Team1 is NOT NULL']);
                // if ($event->date < date('Y-m-d H:i:s', strtotime('last month')) && !$match) continue;
                $guest = $guestTable->fetchOne([
                    'userId'  => $this->getUser()->id,
                    'eventId' => $event->id
                ]);

                $count = $guestTable->count([
                    'eventId' => $event->id,
                    'response' => Model\Guest::RESP_OK
                ]);

                if ($guest->response == Model\Guest::RESP_OK) {
                    $className = 'event-green';
                } else if ($guest->response == Model\Guest::RESP_NO) {
                    $className = 'event-red';
                } else if ($guest->response == Model\Guest::RESP_INCERTAIN) {
                    $className = 'event-orange';
                } else {
                    $className = 'event-default';
                }

                $eventDate = \Datetime::createFromFormat('Y-m-d H:i:s', $event->date);
                $result[]  = [
                    'title'        => $event->name,
                    'start'        => $eventDate->format('Y-m-d H:i'),
                    'url'          => $config['baseUrl'] . '/event/detail/' . $event->id,
                    'className'    => $className,
                    'count'        => $count,
                    'place'        => $event->place,
                    'address'      => $event->address,
                    'zipcode'      => $event->zipCode,
                    'city'         => $event->city,
                    'urlOk'        => $config['baseUrl'] . '/guest/response/' . $event->id . '/' . Model\Guest::RESP_OK,
                    'urlNo'        => $config['baseUrl'] . '/guest/response/' . $event->id . '/' . Model\Guest::RESP_NO,
                    'urlIncertain' => $config['baseUrl'] . '/guest/response/' . $event->id . '/' . Model\Guest::RESP_INCERTAIN,
                ];
            }

            $view = new ViewModel(['result' => $result]);
            $view->setTerminal(true);
            $view->setTemplate('api/default/json.phtml');
            return $view;
        }
    }

}