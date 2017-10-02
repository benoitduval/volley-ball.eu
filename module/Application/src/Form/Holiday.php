<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class Holiday extends Form
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
                'required'    => 'required',
                'class'       => 'form-control datepicker',
                'placeholder' => 'ex: 26/12/2017'
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => Element\Text::class,
            'attributes' => [
                'required'    => 'required',
                'class'       => 'form-control datepicker',
                'placeholder' => 'ex: 29/12/2017'
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