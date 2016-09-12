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

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $signInForm = new SignIn();
        $signUpForm = new SignUp();
        $config     = $this->getContainer()->get('config');
        $baseUrl    = $config['baseUrl'];
        $groups     = [];
        $result     = [];

        if ($this->getUser()) {
            $guestTable     = $this->getContainer()->get(TableGateway\Guest::class);
            $groupTable     = $this->getContainer()->get(TableGateway\Group::class);
            $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
            $eventTable     = $this->getContainer()->get(TableGateway\Event::class);

            $today = new \DateTime('today midnight');
            foreach ($userGroupTable->fetchAll(['userId' => $this->getUser()->id]) as $userGroup) {
                $groupIds[] = $userGroup->groupId;
                $groups[$userGroup->groupId] = $groupTable->find($userGroup->groupId);
            }

            $events = $eventTable->fetchAll([
                'groupId'   => $groupIds,
                'date >= ?' => $today->format('Y-m-d H:i:s')
            ], 'date ASC');

            $counters = [];
            foreach ($events as $event) {
                $eventIds[] = $event->id;
                $userEvents[$event->id] = $event;

                $guest = $guestTable->fetchOne([
                    'userId'  => $this->getUser()->id,
                    'eventId' => $event->id
                ]);

                $counters = $guestTable->getCounters($event->id);
                $result[$guest->id] = [
                    'group'   => $groups[$guest->groupId],
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
    }

    public function exampleAction()
    {
        return new ViewModel([]);
    }

}
