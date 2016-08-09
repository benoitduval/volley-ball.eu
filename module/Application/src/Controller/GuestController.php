<?php
namespace Volley\Controller;

use Zend\View\Model\ViewModel;
use Volley\Form\CreateGuest;
use Volley\Form\CreateGuestValidator as Validator;
use Volley\Entity\Guest;
use Volley\Entity\Group;
use Volley\Services\Mail;

class GuestController extends BaseController
{
    public function responseAction()
    {
        $referer = false;
        if ($this->getRequest()->getHeader('Referer')) {
            $referer = $this->getRequest()->getHeader('Referer')->getUri();
        }

        $eventId     = $this->params('eventId');
        $event       = $this->_getMapper('event')->getById($eventId);
        $responseId  = $this->params('responseId', Guest::RESP_NO_ANSWER);
        $guestMapper = $this->_getMapper('guest');
        $guest       = $guestMapper->fetchOne(array('eventId' => $eventId, 'userId' => $this->user->id));
        $group       = $this->_getMapper('group')->getById($event->groupId);
        if ($event->getDate()->format('Ymd') >= date('Ymd') || $group->isAdmin($this->user->id)) {
            if ($guest->response != $responseId) {
                $guest->response = $responseId;
                $guestMapper->setEntity($guest)->save();
            }
            $this->flashMessenger()->addMessage('Réponse prise en compte');
        } else {
            $this->flashMessenger()->addErrorMessage('Impossible de modifier un événement passé');
        }

        $counters = $guestMapper->getCounters($eventId);

        if ($referer) {
            $this->redirect()->toUrl($referer);
        } else {
            return $this->redirect()->toRoute('volley/event-detail', array(
                'eventId' =>  $eventId,
            ));
        }
    }
}