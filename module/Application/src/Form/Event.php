<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class Place
 */
class Event extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('event');

        $this->setAttributes([
            'method' => 'post',
            'id'     => 'wizardForm'
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'attributes' => [
                'class'       => 'form-control',
                'required'    => 'required',
                'placeholder' => 'ex: Entrainement du Jeudi'
            ],
        ]);

        $this->add([
            'name' => 'date',
            'type' => Element\Text::class,
            'attributes' => [
                'required'    => 'required',
                'class'       => 'form-control datetimepicker',
                'placeholder' => 'ex: 26/12/2017 20:00'
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Commentaire',
            ],
            'attributes' => [
                'class'       => 'form-control',
                'rows'        => 5,
                'placeholder' => 'ex: N\'oubliez pas les bières pour le pot!'
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'place',
            'attributes' => [
                'class'       => 'form-control',
                'required'    => 'required',
                'placeholder' => 'Gymnase Dunois',
            ],
        ]);

        $this->add([
            'name' => 'address',
            'type' => Element\Text::class,
            'attributes' => [
                'required'    => 'required',
                'class'       => 'form-control',
                'placeholder' => '70 rue de Dunois',
            ],
        ]);

        $this->add([
            'name' => 'city',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Ville',
            ],
            'attributes'    => [
                'required'  => 'required',
                'class'     => 'form-control',
                'placeholder' => 'Paris',
            ],
        ]);

        $this->add([
            'name' => 'zipCode',
            'type' => Element\Text::class,
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
                'placeholder' => '75013',
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
                'class' => 'btn btn-info btn-fill btn-wd btn-finish pull-right',
            ],
        ]);
    }
}