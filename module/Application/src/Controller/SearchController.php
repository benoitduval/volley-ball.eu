<?php
namespace Application\Controller;

use RuntimeException;
use Zend\View\Model\ViewModel;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class SearchController extends AbstractController
{
    public function indexAction()
    {
        $query = $this->params()->fromQuery('q', null);
        $events = [];
        $groups = [];
        $query = urldecode($query);
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $groups = $groupTable->fetchAll([
            'name like ?' => '%' . $query . '%'
        ]);

        if ($this->getUser()) {
            $userGroupTable = $this->getContainer()->get(TableGateway\UserGroup::class);
            $userGroups = $userGroupTable->fetchAll(['userId' => $this->getUser()->id]);
            foreach ($userGroups as $userGroup) {
                $groupIds[] = $userGroup->groupId;
            }

            if ($groupIds) {
                $eventTable = $this->getContainer()->get(TableGateway\Event::class);
                $events = $eventTable->fetchAll([
                    'name like ?' => '%' . $query . '%',
                    'groupId' => $groupIds
                ], 'date DESC');
            }
        }

        $this->layout()->user = $this->getUser();
        return new ViewModel([
            'groups' => $groups,
            'events' => $events,
            'query'  => $query,
            'user'   => $this->getUser(),
        ]);
    }
}