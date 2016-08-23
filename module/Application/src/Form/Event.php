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

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Nom de l\'évènement',
            ],
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name' => 'date',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Date',
            ],
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control',
                'id'    => 'datepicker',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Commentaire',
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