<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class RecurentController extends AbstractController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if ($this->_id = $this->params('id')) {
            $groupTable = $this->get(TableGateway\Group::class);
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            if($this->_group = $groupTable->find($this->_id)) {
                $this->_isAdmin  = $userGroupTable->isAdmin($this->getUser()->id, $this->_id);
                $this->_isMember = $userGroupTable->isMember($this->getUser()->id, $this->_id);
            }
        }
        return parent::onDispatch($e);
    }

    public function allAction()
    {
        if ($this->_group && $this->_isAdmin) {
            $recurentTable = $this->get(TableGateway\Recurent::class);
            $recurents = $recurentTable->fetchAll([
                'groupId' => $this->_id
            ]);

            return new ViewModel([
                'group'  => $this->_group,
                'recurents' => $recurents,
                'user'   => $this->getUser(),
                'isAdmin' => true,
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function deleteAction()
    {
        if ($this->_group && $this->_isAdmin) {
            $recurentId = $this->params('recurentId');
            $recurentTable = $this->get(TableGateway\Recurent::class);
            $recurent = $recurentTable->find($recurentId);
            $recurentTable->delete($recurent);
            $this->flashMessenger()->addMessage('Vous avez supprimé avec succès cet évènement récurrent.');
            $this->redirect()->toRoute('recurent', ['action' => 'all', 'id' => $this->_id]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function createAction()
    {
        if ($this->_group && $this->_isAdmin) {
            $recurentTable = $this->get(TableGateway\Recurent::class);

            $form = new Form\Recurent;
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['groupId'] = $this->_group->id;
                    $data['status'] = Model\Recurent::ACTIVE;

                    $recurentTable->save($data);
                }

                $this->flashMessenger()->addMessage('Vous avez créé votre évènement récurrent.');
                $this->redirect()->toRoute('recurent', ['action' => 'all', 'id' => $this->_id]);
            }

            return new ViewModel([
                'group'  => $this->_group,
                'form'   => $form,
                'user'   => $this->getUser(),
                'isAdmin' => true,
            ]);

        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }

    public function editAction()
    {
        $recurentId = $this->params('recurentId');
        if ($this->_group && $this->_isAdmin) {
            $recurentTable = $this->get(TableGateway\Recurent::class);
            $recurent = $recurentTable->find($recurentId);

            $form = new Form\Recurent;
            $form->setData($recurent->toArray());
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['groupId'] = $this->_group->id;
                    $data['status'] = Model\Recurent::ACTIVE;

                    $recurent->exchangeArray($data);
                    $recurentTable->save($recurent);
                }

                $this->flashMessenger()->addMessage('Vous avez édité avec succès cet évènement récurrent.');
                $this->redirect()->toRoute('recurent', ['action' => 'all', 'id' => $this->_id]);
            }

            return new ViewModel([
                'group'  => $this->_group,
                'form'   => $form,
                'user'   => $this->getUser(),
                'isAdmin' => true,
            ]);
        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}