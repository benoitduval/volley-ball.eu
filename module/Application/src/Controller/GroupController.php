<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;
use Application\Form;
use Application\Model;

class GroupController extends AbstractController
{

    public function CreateAction()
    {

        $groupForm  = new Form\Group;
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $config     = $this->getContainer()->get('config');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $groupForm->setData($request->getPost());
            if ($groupForm->isValid()) {

                $data  = $groupForm->getData();
                $group              = New Model\Group();
                $group->name        = ucfirst($data['name']);
                $group->userId      = $this->getUser()->id;
                $group->userIds     = json_encode([$this->getUser()->id]);
                $group->weather     = ($data['weather'] == 'on');
                $group->showUsers   = ($data['showUsers'] == 'on');
                $group->enable      = ($data['enable'] == 'on');
                $group->ffvbUrl     = $data['ffvbUrl'];
                $group->description = $data['description'];
                $group->info        = $data['info'];
                $brand              = $group->initBrand();

                $groupTable->save($group);

                $this->flashMessenger()->addMessage('Votre groupe est maintenant actif. Vous pouvez désormais le partager avec d\'autres personnes et commencer à créer vos évènements.');
                return $this->redirect()->toRoute('home');
            }
        }

        $baseUrl = $config['baseUrl'];

        return new ViewModel(array(
            'form'        => $groupForm,
            'user'        => $this->getUser(),
            'group'       => isset($group) ? $group : '',
            'baseUrl'     => $baseUrl,
        ));
    }

}
