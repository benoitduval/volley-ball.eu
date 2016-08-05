<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Model;
use Application\Form\SignInForm;
use Application\Form\SignUpForm;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;

class AuthController extends AbstractController
{
    public function signinAction()
    {
        if (!($user = $this->getUser())) {
            $signInForm = new SignInForm();
            $request    = $this->getRequest();

            if ($request->isPost()) {
                $signInForm->setData($request->getPost());
                if ($signInForm->isValid()) {
                    $data = $signInForm->getData();

                   $authService = $this->getContainer()->get(AuthenticationService::class);
                   if (!$authService->hasIdentity()) {
                       $adapter  = $authService->getAdapter();
                       $adapter->setIdentity($data['email']);
                       $adapter->setCredential($data['password']);
                       $authService->authenticate();
                       $this->setActiveUser($authService->getIdentity());
                   }
                }
            }
        }

        $this->redirect()->toRoute('home');
    }

    public function signoutAction()
    {
        $authService = $this->getContainer()->get(AuthenticationService::class);
        $authService->clearIdentity();
        $this->setActiveUser(null);

        $this->redirect()->toRoute('home');
    }

    public function signupAction()
    {
        $signUpForm = new SignUpForm();
        $request    = $this->getRequest();

        if ($request->isPost()) {
            $signUpForm->setData($request->getPost());
            if ($signUpForm->isValid()) {
                $data = $signUpForm->getData();

                $user = new Model\User();
                $user->exchangeArray($data);

                $userTable = $this->getContainer()->get(TableGateway\User::class);
                $userTable->save($user);

                $authService = $this->getContainer()->get(AuthenticationService::class);
                if (!$authService->hasIdentity()) {
                    $adapter  = $authService->getAdapter();
                    $adapter->setIdentity($user->email);
                    $adapter->setCredential($user->password);
                    $authService->authenticate();
                    $this->setActiveUser($user);
                }
            }
        }

        $this->redirect()->toRoute('home');
    }
}