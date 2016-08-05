<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class SignUpForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('signup');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'firstname',
            'options' => [
                'label' => 'Prénom',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Prénom',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'lastname',
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Nom',
            ],
        ]);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'E-mail',
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Mot de passe',
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'repassword',
            'options' => [
                'label' => 'Password (Re-type)',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Mot de passe (confirmation)',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Enregistrer',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}