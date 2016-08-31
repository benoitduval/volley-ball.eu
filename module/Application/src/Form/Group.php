<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class Group extends Form
{
	public function __construct($name = null)
	{
        parent::__construct('group');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Nom du groupe',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Nom du groupe',
                'required' => 'required',
            ],
        ]);


        $this->add([
            'name' => 'description',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'placeholder' => 'Description du club, équipes etc',
                'class' => 'form-control',
            ],
        ]);


        $this->add([
            'name' => 'info',
            'type' => Element\Textarea::class,
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Information concernant les séances (niveau, jour, horaire ...)',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Connexion',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}