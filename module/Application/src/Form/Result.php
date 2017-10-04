<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class Result extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('result');

        $this->setAttributes([
            'method' => 'post',
            'id'     => 'wizardForm'
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set1Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set1Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set2Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set2Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set3Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set3Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => true
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set4Team1',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set4Team2',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set5Team1',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set5Team2',
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set1ServeFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set2ServeFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set3ServeFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set4ServeFault',
            'readonly' => true,
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
                'required' => false,
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set5ServeFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set1ServePoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set2ServePoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set3ServePoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set4ServePoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set5ServePoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set1RecepFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set2RecepFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set3RecepFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set4RecepFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set5RecepFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set1AttackFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set2AttackFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set3AttackFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set4AttackFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set5AttackFault',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set1AttackPoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set2AttackPoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set3AttackPoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set4AttackPoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'set5AttackPoint',
            'attributes' => [
                'value' => 0,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'debrief',
            'attributes' => [
                'class' => 'form-control',
                'rows' => '8',
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