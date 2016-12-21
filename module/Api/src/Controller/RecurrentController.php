<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;

class RecurrentController extends AbstractController
{

    public function enableAction()
    {
        $id     = $this->params('id', null);
        $status = $this->params('status', null);

        $recurrentTable = $this->get(TableGateway\Recurent::class);
        $recurrent = $recurrentTable->find($id);
        $recurrent->status = $status;
        $recurrentTable->save($recurrent);

        $view = new ViewModel(array(
            'result'   => [
                'success'  => true
            ]
        ));

        $view->setTerminal(true);
        $view->setTemplate('api/default/json.phtml');
        return $view;
    }
}