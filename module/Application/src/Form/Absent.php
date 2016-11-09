<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class Absent extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('recurent');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'name' => 'from',
            'type' => Element\Text::class,
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control date-only-picker',
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => Element\Text::class,
            'attributes' => [
                'required' => 'required',
                'class' => 'form-control date-only-picker',
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