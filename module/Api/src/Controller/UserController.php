<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;

class UserController extends AbstractController
{

    public function grantAction()
    {
        $userId  = $this->params('userId', null);
        $groupId = $this->params('groupId', null);
        $status  = $this->params('status', null);

        $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
        $userGroup = $userGroupTable->fetchOne(['userId' => $userId, 'groupId' => $groupId]);

        $userGroup->admin = $status;
        $userGroupTable->save($userGroup);

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