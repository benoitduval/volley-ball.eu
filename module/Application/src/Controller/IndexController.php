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
        $this->layout()->user = $this->getUser();

        $menu = [
            [
                'icon'    => 'fa-star',
                'tooltip' => 'Créer un groupe',
                'link'    => 'http://album.dev/event',
                'color'   => 'red',
            ],
            // [
            //     'icon'    => 'fa-star',
            //     'tooltip' => '/example',
            //     'link'    => 'http://album.dev/example',
            //     'color'   => 'yellow darken-1',
            // ],
        ];

        $this->layout()->menu = $menu;
        return new ViewModel([
            'signInForm' => $signInForm,
            'signUpForm' => $signUpForm,
            'user'       => $this->getUser(),
        ]);
    }

    public function detailAction()
    {
        
        return new ViewModel([

        ]);
    }

}
