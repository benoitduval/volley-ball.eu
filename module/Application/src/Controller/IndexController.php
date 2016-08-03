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

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $user        = null;
        $userTable   = $this->container->get(TableGateway\User::class);
        $albumTable  = $this->container->get(TableGateway\Album::class);

        $authService = $this->container->get(AuthenticationService::class);
        if (!$authService->hasIdentity()) {
            $adapter  = $authService->getAdapter();

            $adapter->setIdentity('benoit.duval.pro@gmail.com');
            $adapter->setCredential('test');
            $authResult = $authService->authenticate();
            if ($authResult->isValid()) {
                $user = $authService->getIdentity();
            }
        } else {
            $user = $authService->getIdentity();
        }

        return new ViewModel([
            'user'   => $user,
            'albums' => $albumTable->fetchAll(),
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
