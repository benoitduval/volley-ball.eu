<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Form;
use Application\Model;
use Application\Service;
use Application\TableGateway;
use Application\Service\MailService;

class UserController extends AbstractController
{
    public function indexAction()
    {
        $params = array(
            'firstname' => $this->user->firstname,
            'lastname'  => $this->user->lastname,
            'email'     => $this->user->email
        );

        $notifMapper = $this->_getMapper('notif');

        $notifications = $notifMapper->fetchAll([
            'userId' => $this->user->id,
        ]);

        // pushbullet 
        $config      = $this->getServiceLocator()->get('volley_config');
        $id          = $config['api']['pushbullet']['clientId'];
        $secret      = $config['api']['pushbullet']['secret'];

        $pushMapper  = $this->_getMapper('device');
        $pushEntity  = $pushMapper->fetchOne(['userId' => $this->user->id]);
        $devices = array();
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $url    = 'https://api.pushbullet.com/oauth2/token';
            $method = 'POST';
            $data   = [
                'client_id'     => $id,
                'client_secret' => $secret,
                'code'          => $code,
                'grant_type'    => 'authorization_code',
            ];

            try {
                $response = \Pushbullet\Connection::sendCurlRequest($url, $method, $data);
                $pb       = new \Pushbullet\Pushbullet($response->access_token);
                $devices  = $pb->getDevices();
                foreach ($devices as $device) {
                    $deviceMapper = $this->_getMapper('device');
                    $obj = $deviceMapper->fromArray([
                        'userId' => $this->user->id,
                        'status' => Device::ACTIVE,
                        'iden'   => $device->iden,
                        'name'   => $device->nickname,
                        'token'  => $device->getApiKey(),
                    ])->getEntity();
                    // make sure id is empty to avoid update. Create only here
                    $obj->id = null;
                    $deviceMapper->setEntity($obj)->save();
                }
                return $this->redirect()->toUrl('/user/account');
            } catch (Exception $e) {
                \Zend\Debug\Debug::dump($e->getMessage());die;
            }
        } else if (!is_null($pushEntity)) {
            $deviceMapper = $this->_getMapper('device');
            $devices = $deviceMapper->fetchAll(['userId' => $this->user->id]);
        }


        $form = new EditUser;
        $form->setData($params);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $userMapper = $this->_getMapper('user');
                if (md5($data['password']) == $this->user->password) {
                    if ($data['newPassword'] && $data['passwordVerify'] && $data['newPassword'] == $data['passwordVerify']) {
                        $data['password'] = md5($data['newPassword']);
                        $password = $data['newPassword'];
                    } else {
                        $data['password'] = md5($data['password']);
                    }
                    unset($data['newPassword']);
                    unset($data['passwordVerify']);
                    unset($data['submit']);
                    $data['id'] = $this->user->id;
                    $data['email'] = $this->user->email;
                    $data['status'] = 0;
                    $user = $userMapper->fromArray($data)->save();
                    $authAdapter = $this->getServiceLocator()->get('volley_user_auth');
                    $authAdapter->setParams(
                        array('email' => $data['email'], 'password' => $password)
                    );
                    $result = $authAdapter->authenticate();
                    if ($result->getCode() == 1) {
                        $authStorage = $this->userAuth()->getAuthService()->getStorage();
                        $authStorage->write($result->getIdentity());
                        $this->flashMessenger()->addMessage('Changements enregistrés');
                        $this->redirect()->toRoute('home');
                    }
                } else {
                    $form->get('email')->setMessages(array('Cet email n\'existe pas'));
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Une erreur est survenue');
            }
        }

        return new ViewModel(array(
            'notifications' => $notifications,
            'user'          => $this->user,
            'form'          => $form,
            'clientId'      => $config['api']['pushbullet']['clientId'],
            'redirect'      =>$config['api']['pushbullet']['redirectUrl'],
            'devices'       => $devices,
        ));
    }

    public function paramsAction()
    {
        if ($this->getUser()) {
            $notifTable = $this->get(TableGateway\Notification::class);
            $notifs = $notifTable->fetchAll(['userId' => $this->getUser()->id]);

            return new ViewModel(array(
                'notifications' => $notifs,
                'user'          => $this->getUser(),
            ));

        } else {
            $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas accéder à cette page, vous avez été redirigé sur votre page d\'accueil');
            $this->redirect()->toRoute('home');
        }
    }
}
