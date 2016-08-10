<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;

class PlaceController extends AbstractController
{
    public function createAction()
    {

        if (!$this->getUser()) {
            $this->flashMessenger()->addErrorMessage('<p>Désolé, vous devez être authentifié pour accéder à cette page.</p>
                <p>Vous avez été redirigé sur la page d\'acceuil.</p>');
            $this->redirect()->toRoute('home');
            return;
        }

        // TODO Check permissions
        $groupId    = $this->params()->fromRoute('id');
        $url        = $this->params()->fromQuery('url', '');
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $group      = $groupTable->find($groupId);
        $form       = new Form\Place();

        if ($group && $group->isAdmin($this->getUser())) {
            $placeTable = $this->getContainer()->get(TableGateway\Place::class);

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

                    $this->flashMessenger()->addMessage('Une nouvelle adresse a été ajouté au groupe, vous pouvez maintenant l\'utiliser pour créer un évènement.');
                    if ($url) {
                        $this->redirect()->toUrl(urldecode($url));
                    } else {
                        $this->redirect()->toRoute('place', ['action' => 'all', 'id' => $group->id]);
                    }
                } else {
                    $this->flashMessenger()->addErrorMessage('Une erreur est survenue lors de l\'ajout de l\'adresse');
                }
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Une erreur est survenue, elle peut être causée par :
                <ul>
                    <li>Vous n\'avez pas les droits pour accéder à cette page</li>
                    <li>Le groupe n\'existe plus</li>
                </ul>
                Nous vous redirigeons vers la page d\'accueil
            ');
            $this->redirect()->toRoute('home');
        }

        return new ViewModel([
            'group' => $group,
            'form'  => $form,
            'user'  => $this->getUser(),
        ]);
    }

    public function allAction()
    {
        if (!$this->getUser()) {
            $this->flashMessenger()->addErrorMessage('<p>Désolé, vous devez être authentifié pour accéder à cette page.</p>
                <p>Vous avez été redirigé sur la page d\'acceuil.</p>');
            $this->redirect()->toRoute('home');
            return;
        }

        $groupId = $this->params()->fromRoute('id');
        $placeTable = $this->getContainer()->get(TableGateway\Place::class);
        $groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $group = $groupTable->find($groupId);
        $places = $placeTable->fetchAll(['groupId' => $groupId]);

        return new ViewModel([
            'group'  => $group,
            'places' => $places,
            'user'   => $this->getUser(),
        ]);
    }
}
