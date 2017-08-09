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
use Application\Model;
use Application\Service\MailService;


class IndexController extends AbstractController
{
    public function indexAction()
    {
        if ($this->getUser()) {

            $guestTable = $this->get(TableGateway\Guest::class);

            $userId     = $this->getUser()->id;
            $groups     = $this->getUserGroups();
            $result     = [];
            $counters   = [];
            if ($groups) {
                foreach ($groups as $group) {
                    $userGroups[$group->id] = $group;
                }

                if ($this->getUser()->display != Model\User::DISPLAY_TABLE) {
                    foreach ($this->get(TableGateway\Event::class)->getActiveByUserId($userId) as $event) {
                        $eventIds[] = $event->id;

                        $guest = $guestTable->fetchOne([
                            'userId'  => $userId,
                            'eventId' => $event->id
                        ]);
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);

                        $result[$guest->id] = [
                            'group'   => $userGroups[$guest->groupId],
                            'event'   => $event,
                            'guest'   => $guest,
                            'date'    => $date,
                        ];
                    }
                }
            }

            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'events'       => $result,
                'user'         => $this->getUser(),
                'groups'       => $groups,
            ]);
        } else {
            return $this->redirect()->toRoute('welcome');
        }
    }

    public function welcomeAction()
    {
        $this->layout()->setTemplate('layout/welcome.phtml');
        if (!$this->getUser()) {
            $signInForm = new SignIn();
            $signUpForm = new SignUp();

            $this->layout()->user = $this->getUser();
            $this->layout()->signInForm = $signInForm;
            $this->layout()->signUpForm = $signUpForm;
            return new ViewModel([
                'signInForm' => $signInForm,
                'signUpForm' => $signUpForm,
            ]);
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

}
