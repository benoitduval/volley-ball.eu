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
use Application\Form\SignInForm;
use Application\Form\SignUpForm;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $signInForm = new SignInForm();
        $signUpForm = new SignUpForm();
        $this->layout()->user = $this->getUser();

        return new ViewModel([
            'signInForm' => $signInForm,
            'signUpForm' => $signUpForm,
            'user'       => $this->getUser(),
        ]);
    }

    public function addAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

}
