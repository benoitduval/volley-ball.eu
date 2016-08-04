<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;

class AuthController
{
    public function signinAction()
    {
        // // $albumTable  = $this->getContainer()->get(TableGateway\Album::class);

        // $user = $this->getUser();

        // $signInForm = new SignInForm();
        // $request = $this->getRequest();

        // if ($request->isPost()) {
        //     $signInForm->setData($request->getPost());
        //     if ($signInForm->isValid()) {
        //         $data = $signInForm->getData();

        //        $authService = $this->getContainer()->get(AuthenticationService::class);
        //        if (!$authService->hasIdentity()) {
        //            $adapter  = $authService->getAdapter();
        //            $adapter->setIdentity($data['email']);
        //            $adapter->setCredential($data['password']);
        //            $authService->authenticate();
        //            $this->setActiveUser($authService->getIdentity());
        //        }
        //     }
        // }

        // $this->layout()->user = $this->getUser();
        // return new ViewModel([
        //     'signInForm' => $signInForm,
        //     'user'   => $user,
        // ]);
    }
}
