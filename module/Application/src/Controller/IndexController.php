<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use \Application\TableGateway;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $userTable  = $this->container->get(TableGateway\User::class);
        $albumTable = $this->container->get(TableGateway\Album::class);
        return new ViewModel([
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
