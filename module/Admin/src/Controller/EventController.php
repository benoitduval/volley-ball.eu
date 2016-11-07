<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;

class EventController extends AbstractController
{

    public function indexAction()
    {
    	$this->layout()->setTemplate('admin/layout/layout.phtml');
        return new ViewModel([
        ]);
    }
}