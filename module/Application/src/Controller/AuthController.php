<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\SignInForm;
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
        $signInForm  = new SignInForm();
        $request     = $this->getRequest();
        $authService = $this->getContainer()->get(AuthenticationService::class);
        $authService->clearIdentity();
        $this->setActiveUser(null);

        $this->redirect()->toRoute('home');
    }
}