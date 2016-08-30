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
                $group->showUsers   = ($data['showUsers'] == 'on');
                $group->enable      = ($data['enable'] == 'on');
                $group->description = $data['description'];
                $group->info        = $data['info'];
                $brand              = $group->initBrand();

                $groupTable->save($group);

                $this->flashMessenger()->addMessage('
                    Votre groupe est maintenant actif.<br/>
                    Depuis cette page, le bouton d\'action en bas à droite vous permet de:
                    <ul>
                        <li>Créer un évènement ponctuel (match)</li>
                        <li>Créer un évènement récurrent (entrainement)</li>
                        <li>Partager votre groupe</li>
                        <li>Gérer les informations du groupe</li>
                        <li>Gérer les demandes pour rejoindre le groupe</li>
                        <li>Gérer les membres ainsi que leurs permissions</li>
                    </ul>
                ');
                return $this->redirect()->toRoute('home');
            }
        }

        $baseUrl = $config['baseUrl'];

        return new ViewModel(array(
            'form'    => $groupForm,
            'user'    => $this->getUser(),
            'group'   => isset($group) ? $group : '',
            'baseUrl' => $baseUrl,
        ));
    }

    public function detailAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $group = $groupTable->find($id);
        $config = $this->getContainer()->get('config');
        $baseUrl = $config['baseUrl'];
        $menu = [
            [
                'tooltip' => 'Partager',
                'icon'    => 'share',
                'link'    => $baseUrl . '/group/share',
                'color'   => 'indigo',
            ],
            [
                'tooltip' => 'Éditer',
                'icon'    => 'edit',
                'link'    => $baseUrl . '/group/edit',
                'color'   => 'dark-green',
            ],
            [
                'tooltip' => 'Membres',
                'icon'    => 'group_add',
                'link'    => $baseUrl . '/group/member',
                'color'   => 'amber',
            ],
            [
                'tooltip' => 'Adresses',
                'icon'    => 'add_location',
                'link'    => $baseUrl . '/place/all/' . $id,
                'color'   => 'light-blue',
            ],
            [
                'tooltip' => 'Évèn. récurrent',
                'icon'    => 'repeat',
                'link'    => $baseUrl . '/group/recurrent-event',
                'color'   => 'orange',
            ],
            [
                'tooltip' => 'Évèn. ponctuel',
                'icon'    => 'event',
                'link'    => $baseUrl . '/group/event',
                'color'   => 'pink',
            ],
        ];

        $this->layout()->menu = $menu;
        return new ViewModel(array(
            'group' => $group,
            'user'  => $this->getUser(),
        ));
    }

}
