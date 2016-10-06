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
                'rows' => 4,
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'gymnasium',
            'type' => Element\Text::class,
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);


        $this->add([
            'name' => 'address',
            'type' => Element\Text::class,
            'attributes' => [
                'rows' => 4,
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'schedule',
            'type' => Element\Textarea::class,
            'attributes' => [
                'rows' => 4,
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'long',
            'type' => Element\Text::class,
            'attributes' => [
                'rows' => 4,
                'class' => 'form-control',
            ],
        ]);

        $this->add([

            'name' => 'lat',
            'type' => Element\Text::class,
            'attributes' => [
                'rows' => 4,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Enregistrer',
                'class' => 'btn btn-finish btn-fill btn-primary',
            ],
        ]);
    }
}