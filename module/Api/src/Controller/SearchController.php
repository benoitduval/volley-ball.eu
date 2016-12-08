<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\AbstractController;
use Application\Model;
use Application\TableGateway;
use Application\Service\MailService;

class SearchController extends AbstractController
{

    public function dataAction()
    {
        $groupTable = $this->get(TableGateway\Group::class);
        $groups = $groupTable->fetchAll();

        $data['result'] = [];
        foreach ($groups as $group) {
            $data['result']['groups'][] = [
                'label' => $group->name,
                'date' => $group->id,
                'link' => $group->getPublicLink(),
            ];
        }

        if ($this->getUser()) {
            $userGroupTable = $this->get(TableGateway\UserGroup::class);
            $userGroups = $userGroupTable->fetchAll(['userId' => $this->getUser()->id]);
            foreach ($userGroups as $userGroup) {
                $groupIds[] = $userGroup->groupId;
            }

            if (isset($groupIds)) {
                $eventTable = $this->get(TableGateway\Event::class);
                $events = $eventTable->fetchAll([
                    'groupId' => $groupIds,
                ]);
                foreach ($events as $event) {
                    if ($match = $this->get(TableGateway\Match::class)->fetchOne(['eventId' => $event->id])) {
                        $data['result']['matchs'][] = [
                            'label' => $event->name,
                            'date' => $event->date,
                            'link' => '/event/detail/' . $event->id,
                        ];
                    }
                }

                if (!isset($data['result']['matchs'])) {
                    $data['result']['matchs'][] = [
                        'label' => 'Aucun Résultat',
                        'date' => '',
                        'link' => 'javascritp:void();',
                    ];
                }
            }
        }

        $view = new ViewModel($data);

        $view->setTerminal(true);
        $view->setTemplate('api/default/json.phtml');
        return $view;
    }
}