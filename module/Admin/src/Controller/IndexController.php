<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;
use Admin\Form;

class IndexController extends AbstractController
{

    public function indexAction()
    {
        $users  = [];
        $groups = [];
        $events = [];

        $form = new Form\Search();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                if (!$data['user-display']) $data['user-display'] = '';
                if (!$data['user-status']) $data['user-status'] = '';

                $where = [];
                foreach ($data as $key => $value) {
                    if ($value) {
                        $table = preg_match('/(user|group|event)-(.*)/', $key, $matches);
                        if ($matches) $where[$matches[1]][$matches[2]] = $value;
                    }
                }

                if (isset($where['user'])) {
                    $userTable = $this->get(TableGateway\User::class);
                    $users = $userTable->fetchAll($where['user']);
                }

                if (isset($where['group'])) {
                    $userTable = $this->get(TableGateway\Group::class);
                    $groups = $userTable->fetchAll($where['group']);
                }

                if (isset($where['event'])) {
                    $userTable = $this->get(TableGateway\Event::class);
                    $events = $userTable->fetchAll($where['event']);
                }
            }
        }

        $this->layout()->setTemplate('admin/layout/layout.phtml');
        return new ViewModel([
            'form' => $form,
            'users' => $users,
            'groups' => $groups,
            'events' => $events,
        ]);
    }
}