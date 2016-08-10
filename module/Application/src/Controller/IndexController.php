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
use Application\Form\SignIn;
use Application\Form\SignUp;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $signInForm = new SignIn();
        $signUpForm = new SignUp();
        $config     = $this->getContainer()->get('config');
        $baseUrl    = $config['baseUrl'];
        $this->layout()->user = $this->getUser();

        $menu = false;
        if ($this->getUser()) {
            $menu = [
                [
                    'icon'    => 'group',
                    'tooltip' => 'Créer un groupe',
                    'link'    => $baseUrl . '/group/create',
                    'color'   => 'red',
                ],
            ];
        }

        $this->layout()->menu = $menu;
        return new ViewModel([
            'signInForm' => $signInForm,
            'signUpForm' => $signUpForm,
            'user'       => $this->getUser(),
        ]);
    }

    public function exampleAction()
    {
        return new ViewModel([]);
    }

}
