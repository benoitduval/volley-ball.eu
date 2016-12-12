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
use Application\Model;
use Application\Service\MailService;


class IndexController extends AbstractController
{
    public function indexAction()
    {
        if ($this->getUser()) {
            $signInForm = new SignIn();
            $signUpForm = new SignUp();

            $guestTable = $this->get(TableGateway\Guest::class);

            $config     = $this->get('config');
            $baseUrl    = $config['baseUrl'];

            $userId     = $this->getUser()->id;
            $groups     = $this->getUserGroups();
            $result     = [];
            $counters   = [];
            if ($groups) {
                foreach ($groups as $group) {
                    $userGroups[$group->id] = $group;
                }

                foreach ($this->get(TableGateway\Event::class)->getActiveByUserId($userId) as $event) {
                    $eventIds[] = $event->id;

                    $guest = $guestTable->fetchOne([
                        'userId'  => $userId,
                        'eventId' => $event->id
                    ]);

                    $counters = $guestTable->getCounters($event->id);
                    $result[$guest->id] = [
                        'group'   => $userGroups[$guest->groupId],
                        'event'   => $event,
                        'guest'   => $guest,
                        'ok'      => $counters[Model\Guest::RESP_OK],
                        'no'      => $counters[Model\Guest::RESP_NO],
                        'perhaps' => $counters[Model\Guest::RESP_INCERTAIN],
                        'date'    => \DateTime::createFromFormat('Y-m-d H:i:s', $event->date),
                    ];
                }
            }

            $this->layout()->user = $this->getUser();
            return new ViewModel([
                'events'     => $result,
                'signInForm' => $signInForm,
                'signUpForm' => $signUpForm,
                'user'       => $this->getUser(),
                'groups'     => $groups,
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
