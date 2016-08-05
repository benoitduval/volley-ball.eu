<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Model;
use Application\Form\SignInForm;
use Application\Form\SignUpForm;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;
use Application\Service\MailService;
use Zend\Crypt\Password\Bcrypt;
use Application\Service\Storage\Cookie as CookieStorage;

class AuthController extends AbstractController
{
    public function signinAction()
    {
        if (!($user = $this->getUser())) {
            $signInForm = new SignInForm();
            $request    = $this->getRequest();

            if ($request->isPost()) {
                $signInForm->setData($request->getPost());
                if ($signInForm->isValid()) {
                    $data = $signInForm->getData();

                   $authService = $this->getContainer()->get(AuthenticationService::class);
                   if (!$authService->hasIdentity()) {
                       $adapter  = $authService->getAdapter();
                       $adapter->setIdentity($data['email']);
                       $adapter->setCredential($data['password']);
                       $authService->authenticate();
                       $this->setActiveUser($authService->getIdentity());
                   }
                }
            }
        }

        $this->redirect()->toRoute('home');
    }

    public function signoutAction()
    {
        $authService = $this->getContainer()->get(AuthenticationService::class);
        $authService->clearIdentity();
        $this->setActiveUser(null);

        $this->redirect()->toRoute('home');
    }

    public function signupAction()
    {
        $signUpForm = new SignUpForm();
        $request    = $this->getRequest();

        if ($request->isPost()) {
            $signUpForm->setData($request->getPost());
            if ($signUpForm->isValid()) {
                $data = $signUpForm->getData();

                $userTable = $this->getContainer()->get(TableGateway\User::class);
                if ($userTable->fetchOne(['email' => $data['email']])) {
                    $this->flashMessenger()->addErrorMessage('Il est impossible de créer un compte avec l\'adresse <b>' . $data['email'] . '</b> Cette adresse email est déjà utilisée. Merci de recommencer en changeant votre adresse.');
                } elseif ($data['password'] != $data['repassword']) {
                    $this->flashMessenger()->addErrorMessage('Les <b>mots de passe</b> ne correspondent pas. Merci de recommencer votre inscription.');
                } else {
                    $user   = new Model\User();
                    $bCrypt = new Bcrypt();
                    $data['status']  = Model\User::HAS_TO_CONFIRM;
                    $data['display'] = Model\User::DISPLAY_LARGE;
                    $data['password'] = $bCrypt->create(md5($data['password']));

                    $user->exchangeArray($data);
                    $userTable->save($user);

                    // Activation mail
                    $mail   = $this->getContainer()->get(MailService::class);
                    $config = $this->getContainer()->get('config');
                    $salt   = $config['salt'];
                    $mail->addTo($user->email);
                    $mail->setSubject('[Volley-ball.eu] Confirmation de compte');
                    $token = md5($user->email . $config['salt']);

                    $mail->setTemplate(MailService::TEMPLATE_ACCOUNT_VERIFY, array(
                        'email' => $user->email,
                        'url'   => 'http://mvc.dev/auth/verify?email=' . urlencode($user->email) . '&token=' . $token,
                    ));
                    $mail->send();
                    $this->flashMessenger()->addMessage('Un email a été envoyé à l\'adresse <b>' . $user->email . '</b> afin de confirmer la création de compte');
                }
            }
        }
        $this->redirect()->toRoute('home');
    }

    public function verifyAction()
    {
        $config = $this->getContainer()->get('config');
        $email      = $this->params()->fromQuery('email');
        $paramToken = $this->params()->fromQuery('token');
        $userTable  = $this->getContainer()->get(TableGateway\User::class);
        if ($user = $userTable->fetchOne(['email' => $email])) {
            $token = md5($user->email . $config['salt']);
            if ($paramToken == $token) {
                $user->status = Model\User::CONFIRMED;
                $userTable->save($user);

                $authService = $this->getContainer()->get(AuthenticationService::class);
                if (!$authService->hasIdentity()) {
                    $authService->getStorage()->write($user->email);
                    $this->setActiveUser($user);
                    $this->flashMessenger()->addMessage('Votre compte est maintenant actif. Merci d\'utiliser http://volley-ball.eu.');
                }
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Désolé, nous n\'avons pas pu confirmer votre compte, un erreur est survenue lors de cette vérification. Merci de me contacter afin de régler ce soucis.');
        }
        $this->redirect()->toRoute('home');
    }
}