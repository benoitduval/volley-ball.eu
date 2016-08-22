<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class Place
 */
class Place extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('place');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Nom du Gymnase\Lieu',
            ],
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'address',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Adresse',
            ],
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'city',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Ville',
            ],
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'zipCode',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Code Postal',
            ],
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'long',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Longitude',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'lat',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Latittude',
            ],
            'attributes' => [
                'class' => 'form-control',
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