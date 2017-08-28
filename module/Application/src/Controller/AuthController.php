<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Model;
use Application\Form\SignIn;
use Application\Form\SignUp;
use Application\Form\Password;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;
use Application\Service\MailService;
use Zend\Crypt\Password\Bcrypt;
use Application\Service\Storage\Cookie as CookieStorage;

class AuthController extends AbstractController
{
    public function signinAction()
    {
        $this->layout()->setTemplate('layout/signin.phtml');

        if ($this->getRequest()->getHeader('Referer')) {
            $referer = $this->getRequest()->getHeader('Referer')->getUri();
        }

        if (!($user = $this->getUser())) {
            $signInForm = new SignIn();
            $request    = $this->getRequest();

            if ($request->isPost()) {
                $signInForm->setData($request->getPost());
                if ($signInForm->isValid()) {
                    $data = $signInForm->getData();
                    $authService = $this->get(AuthenticationService::class);
                    if (!$authService->hasIdentity()) {
                        $adapter  = $authService->getAdapter();
                        $adapter->setIdentity($data['email']);
                        $adapter->setCredential($data['password']);
                        $result = $authService->authenticate();
                        if ($result->isValid()) {
                            $this->setActiveUser($authService->getIdentity());
                            $this->redirect()->toUrl('/');
                        } else {
                            foreach ($result->getMessages() as $message) {
                                $this->flashMessenger()->addErrorMessage($message);
                            }
                        }
                    }
                }
            }

            return new ViewModel([
                'signInForm' => $signInForm
            ]);
        }
    }

    public function signoutAction()
    {
        $authService = $this->get(AuthenticationService::class);
        $authService->clearIdentity();
        $this->setActiveUser(null);

        $this->redirect()->toRoute('home');
    }

    public function signupAction()
    {
        $this->layout()->setTemplate('layout/signup.phtml');

        $referer = false;
        if ($this->getRequest()->getHeader('Referer')) {
            $referer = $this->getRequest()->getHeader('Referer')->getUri();
        }

        $signUpForm = new SignUp();
        $request    = $this->getRequest();

        $token   = $this->params()->fromQuery('token');
        $groupId = $this->params()->fromQuery('id');

        if ($request->isPost()) {
            $signUpForm->setData($request->getPost());
            if ($signUpForm->isValid()) {
                $data = $signUpForm->getData();

                $userTable = $this->get(TableGateway\User::class);
                if ($userTable->fetchOne(['email' => $data['email']])) {
                    $this->flashMessenger()->addErrorMessage('Il est impossible de créer un compte avec l\'adresse <b>' . $data['email'] . '</b> Cette adresse email est déjà utilisée. Merci de recommencer en changeant votre adresse.');
                } elseif ($data['password'] != $data['repassword']) {
                    $this->flashMessenger()->addErrorMessage('Les <b>mots de passe</b> ne correspondent pas. Merci de recommencer votre inscription.');
                } else {

                    $bCrypt = new Bcrypt();
                    $data['status']   = Model\User::CONFIRMED;
                    $data['password'] = $bCrypt->create(md5($data['password']));

                    $user = $userTable->save($data);

                    // create account notifications
                    $notifs = Model\Notification::$labels;
                    $notifTable = $this->get(TableGateway\Notification::class);
                    foreach ($notifs as $id => $label) {
                        $notifTable->save([
                            'userId' => $user->id,
                            'notification' => $id,
                            'status' => Model\Notification::ACTIVE
                        ]);
                    }

                    $authService = $this->get(AuthenticationService::class);
                    $authService->getStorage()->write($user);
                    $this->setActiveUser($user);

                    if ($groupId) {
                        $userGroupTable = $this->get(TableGateway\UserGroup::class);
                        $userGroup = $userGroupTable->save([
                            'groupId' => $groupId,
                            'userId'  => $user->id,
                            'admin'   => Model\UserGroup::MEMBER,
                        ]);

                        $eventTable = $this->get(TableGateway\Event::class);
                        $events = $eventTable->fetchAll([
                            'date > NOW()',
                            'groupId' => $groupId
                        ]);

                        $guestTable  = $this->get(TableGateway\Guest::class);
                        $absentTable = $this->get(TableGateway\Absent::class);
                        foreach ($events as $event) {
                            $absent = $absentTable->fetchOne([
                                'userId'     => $user->id,
                                '`from` < ?' => $event->date,
                                '`to` > ?'   => $event->date,
                            ]);

                            $response = $absent ? Model\Guest::RESP_NO : Model\Guest::RESP_NO_ANSWER;
                            $guest = $guestTable->save([
                                'userId'   => $user->id,
                                'groupId'  => $groupId,
                                'eventId'  => $event->id,
                                'response' => $response,
                            ]);
                        }
                    } 

                    $this->redirect()->toRoute('home');
                }
            }
        }

        return new ViewModel([
            'signUpForm' => $signUpForm
        ]);
    }

    public function verifyAction()
    {
        $config = $this->get('config');
        $email      = $this->params()->fromQuery('email');
        $paramToken = $this->params()->fromQuery('token');
        $userTable  = $this->get(TableGateway\User::class);
        if ($user = $userTable->fetchOne(['email' => $email])) {
            $token = md5($user->email . $config['salt']);
            if ($paramToken == $token) {
                $user->status = Model\User::CONFIRMED;
                $userTable->save($user);

                $authService = $this->get(AuthenticationService::class);
                if (!$authService->hasIdentity()) {
                    $authService->getStorage()->write($user);
                    $this->setActiveUser($user);
                    $this->flashMessenger()->addMessage('Votre compte est maintenant actif. Merci d\'utiliser http://volley-ball.eu.');
                }
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Désolé, nous n\'avons pas pu confirmer votre compte, un erreur est survenue lors de cette vérification. Merci de me contacter afin de régler ce soucis.');
        }
        $this->redirect()->toRoute('home');
    }

    public function emailAction()
    {
         if ($email = $this->params()->fromPost('email')) {
            $userTable = $this->get(TableGateway\User::class);
            if ($user = $userTable->fetchOne(['email' => $email])) {
                $config = $this->get('config');
                $mail   = $this->get(MailService::class);
                $salt   = $config['salt'];
                $mail->addTo($user->email);
                $mail->setSubject('[Volley-ball.eu] Mot de passe oublié');
                $token = md5($user->email . $config['salt']);

                $mail->setTemplate(MailService::TEMPLATE_PASSWORD, array(
                    'email' => $user->email,
                    'url'   => $config['baseUrl'] . '/auth/reset?email=' . urlencode($user->email) . '&token=' . $token,
                ));
                $mail->send();
            }
        }
        return $this->redirect()->toRoute('home');
    }

    public function resetAction()
    {
        $email = $this->params()->fromQuery('email');
        $token = $this->params()->fromQuery('token');
        $userTable = $this->get(TableGateway\User::class);
        if ($user = $userTable->fetchOne(['email' => $email])) {
            $config = $this->get('config');
            $salt   = $config['salt'];
            $verify = md5($user->email . $config['salt']);
            if ($verify == $token) {
                $form = new Password;
                $request    = $this->getRequest();

                if ($request->isPost()) {
                    $form->setData($request->getPost());
                    if ($form->isValid()) {
                        $data = $form->getData();
                        if ($data['password'] == $data['repassword']) {
                            $bCrypt = new Bcrypt();
                            $user->password = $bCrypt->create(md5($data['password']));
                            $userTable->save($user->toArray());
                            $authService = $this->get(AuthenticationService::class);
                            $authService->getStorage()->write($user);
                            $this->setActiveUser($user);
                            $this->flashMessenger()->addMessage('Votre mot de passe est modifié, vous avez été automatiquement identifié.');
                            return $this->redirect()->toRoute('home');
                        }
                    }
                }

                return new ViewModel([
                    'form'   => $form,
                ]);
            }

        }
    }
}