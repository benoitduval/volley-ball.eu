<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Application\Model;

/**
 * Class SignInForm
 */
class SignUp extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('signup');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'firstname',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Prénom',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'lastname',
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
                'placeholder' => 'Nom',
            ],
        ]);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
                'type'     => 'email',
                'placeholder' => 'Email',
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'password',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Mot de passe',
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'repassword',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Confirmation de mot de passe',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'phone',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'licence',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'display',
            'type' => Element\Select::class,
            'attributes' => [
                'class' => 'form-control',
                'required' => false,
            ],
            'options' => [
                'value_options' => [
                    0 => '',
                    Model\User::DISPLAY_SMALL => 'Small',
                    Model\User::DISPLAY_LARGE => 'Large',
                    Model\User::DISPLAY_TABLE => 'Table',
                ],
            ]
        ]);

        $this->add([
            'name' => 'status',
            'type' => Element\Select::class,
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'value_options' => [
                    0 => '',
                    Model\User::CONFIRMED => 'Confirmed',
                    Model\User::HAS_TO_CONFIRM => 'Not Confirmed',
                ],
            ]
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