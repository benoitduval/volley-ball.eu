<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class AbsentController extends AbstractController
{

    public function indexAction()
    {
        if ($this->getUser()) {
            $absentTable = $this->get(TableGateway\Absent::class);
            $absents = $absentTable->fetchAll(['userId' => $this->getUser()->id]);

            return new ViewModel(array(
                'absents' => $absents,
                'user'    => $this->getUser(),
            ));
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function deleteAction()
    {
        if ($this->getUser()) {
            $id = $this->params('id');
            $absentTable = $this->get(TableGateway\Absent::class);
            $absent = $absentTable->find($id);
            $absentTable->delete($absent);
            $this->flashMessenger()->addMessage('Vous avez supprimé avec succès cet évènement récurrent.');
            $this->redirect()->toRoute('absent', ['action' => 'index']);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function createAction()
    {
        if ($this->getUser()) {
            $absentTable = $this->get(TableGateway\Absent::class);

            $form = new Form\Absent;
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $formData = $form->getData();
                    $from = \DateTime::createFromFormat('d/m/Y', $formData['from']);
                    $to   = \DateTime::createFromFormat('d/m/Y', $formData['to']);
                    $absent = $absentTable->save([
                        'from'   => $from->format('Y-m-d 00:00:00'),
                        'to'     => $to->format('Y-m-d 23:59:59'),
                        'userId' => $this->getUser()->id
                    ]);
                }

                $this->_updateEvents($from, $to);

                $this->flashMessenger()->addMessage('Vous avez ajouté une nouvelle absence. Vos disponibilités sont à jour.');
                $this->redirect()->toRoute('absent', ['action' => 'index']);
            }

            return new ViewModel([
                'form'   => $form,
                'user'   => $this->getUser(),
            ]);

        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function editAction()
    {
        $absentId = $this->params('id');
        if ($this->getUser()) {
            $absentTable = $this->get(TableGateway\Absent::class);
            $absent = $absentTable->find($absentId);

            $form = new Form\Absent;
            $from = \DateTime::createFromFormat('Y-m-d H:i:s', $absent->from);
            $to   = \DateTime::createFromFormat('Y-m-d H:i:s', $absent->to);
            $data = [
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'userId' => $absent->userId,
            ];
            $form->setData($data);
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $formData = $form->getData();
                    $from = \DateTime::createFromFormat('d/m/Y', $formData['from']);
                    $to   = \DateTime::createFromFormat('d/m/Y', $formData['to']);

                    $absentTable->save([
                        'id'     => $absentId,
                        'from'   => $from->format('Y-m-d 00:00:00'),
                        'to'     => $to->format('Y-m-d 23:59:59'),
                        'userId' => $this->getUser()->id
                    ]);
                }

                $this->_updateEvents($from, $to);

                $this->flashMessenger()->addMessage('Vous avez édité avec succès cet évènement récurrent.');
                $this->redirect()->toRoute('absent', ['action' => 'index']);
            }

            return new ViewModel([
                'form'   => $form,
                'user'   => $this->getUser(),
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    protected function _updateEvents($from, $to)
    {
        // update all event between theses dates
        $eventTable = $this->get(TableGateway\Event::class);
        $guestTable = $this->get(TableGateway\Guest::class);
        $groupIds   = [];
        foreach ($this->getUserGroups() as $group) {
            $groupIds[] = $group->id;
        }

        if ($groupIds) {
            $events = $eventTable->fetchAll([
                'date > ?' => $from->format('Y-m-d 00:00:00'),
                'date < ?' => $to->format('Y-m-d 23:59:59'),
                'groupId'  => $groupIds
            ]);

            foreach ($events as $event) {
                $guest = $guestTable->fetchOne([
                    'userId'  => $this->getUser()->id,
                    'eventId' => $event->id,
                    'response <> ?' => Model\Guest::RESP_NO 
                ]);

                if ($guest) {
                    $guest->response = Model\Guest::RESP_NO;
                    $guestTable->save($guest);
                }
            }
        }

    }
}