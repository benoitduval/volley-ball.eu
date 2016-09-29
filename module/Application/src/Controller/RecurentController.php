<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class RecurentController extends AbstractController
{
    public function createAction()
    {
        $groupId       = $this->params()->fromRoute('id');
        $groupTable    = $this->getContainer()->get(TableGateway\Group::class);
        $group         = $groupTable->find($groupId);
        $recurentTable = $this->getContainer()->get(TableGateway\Recurent::class);

        $form = new Form\Recurent;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $data['groupId'] = $group->id;
                $data['status'] = Model\Recurent::ACTIVE;

                $recurent = new Model\Recurent;
                $recurent->exchangeArray($data);
                $recurentTable->save($recurent);
            }
        }

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'group'  => $group,
            'form'   => $form,
            'user'   => $this->getUser(),
        ]);
    }
}