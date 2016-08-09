<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class PlaceController extends AbstractController
{
    public function CreateAction()
    {
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $group      = $groupTable->fetchOne(['userId' => $this->getUser()->id]);
        if (!$group) {
            \Zend\Debug\Debug::dump('nop');die;
        }

        $placeTable = $this->getContainer()->get(TableGateway\Place::class);
        $form = new Form\Place();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['groupId'] = $group->id;

                $mapService = $this->getContainer()->get(Service\Map::class);
                $address = $data['address'] . ', ' . $data['zipCode'] . ' ' . $data['city'] . ' France';

                if ($coords = $mapService->getCoordinates($address)) {
                    $data = array_merge($data, $coords);
                }

                $place = new Model\Place();
                $place->exchangeArray($data);

                $placeTable->save($place);
                $this->redirect()->toRoute('home');
            } else {
                // flash messenger error
            }
        }

        return new ViewModel([
            'form'  => $form,
            'user'  => $this->getUser(),
        ]);
    }

    // public function getAction()
    // {
    //     $groupId     = $this->params('groupId');
    //     $placeMapper = $this->_getMapper('place');
    //     $places      = $placeMapper->fetchAll(array('groupId' => $groupId), 'zipCode DESC');
    //     $result      = array();
    //     foreach ($places as $place) {
    //         $result[$place->id] = $place->zipCode . ' ' . $place->city . ' - ' . $place->address;
    //     }

    //     $view = new ViewModel(array(
    //         'result' => $result
    //     ));
    //     $view->setTerminal(true);
    //     $view->setTemplate('volley/api/json.phtml');
    //     return $view;
    // }
}
