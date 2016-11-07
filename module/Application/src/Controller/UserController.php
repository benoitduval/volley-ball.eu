<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;
use Application\Service\MailService;

class UserController extends AbstractController
{

    public function paramsAction()
    {
        if ($this->getUser()) {
            $form = new Form\Signup;
            $form->setData($this->getUser()->toArray());
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                $userTable = $this->get(TableGateway\User::class);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['id'] = $this->getUser()->id;
                    $user = $userTable->save($data);
                    $this->_user = $user;
                }
            }
            $notifTable = $this->get(TableGateway\Notification::class);
            $notifs = $notifTable->fetchAll(['userId' => $this->getUser()->id]);

            return new ViewModel([
                'form'          => $form,
                'notifications' => $notifs,
                'user'          => $this->getUser(),
            ]);

        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}
