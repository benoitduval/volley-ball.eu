<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\TableGateway;

class GuestController extends AbstractController
{

    protected $_event    = null;
    protected $_id       = null;
    protected $_isAdmin  = false;
    protected $_isMembre = false;

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if ($this->_id = $this->params('id')) {
            $eventTable = $this->get(TableGateway\Event::class);
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            if($this->_event = $eventTable->find($this->_id)) {
                $this->_isAdmin  = $userGroupTable->isAdmin($this->getUser()->id, $this->_event->id);
                $this->_isMember = $userGroupTable->isMember($this->getUser()->id, $this->_event->id);
            }
        }
        return parent::onDispatch($e);
    }

    public function responseAction()
    {
        $responseId     = $this->params('response');

        if ($this->_event && $this->_isMember) {
            $guestTable     = $this->get(TableGateway\Guest::class);
            $groupTable     = $this->get(TableGateway\Event::class);
            $guest = $guestTable->fetchOne([
                'eventId' => $this->_id,
                'userId'  => $this->getUser()->id,
            ]);
            $group = $groupTable->find($this->_event->groupId);
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->_event->date);

            if ($date->format('Ymd') >= date('Ymd')) {
                if ($guest->response != $responseId) {
                    $guest->response = $responseId;
                    $guestTable->save($guest);
                }
                $this->flashMessenger()->addMessage('Votre réponse a été prise en compte.');
            } else {    
                $this->flashMessenger()->addErrorMessage('Impossible de modifier un événement passé');
            }

            $this->redirect()->toRoute('event', ['id' => $this->_id]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}