<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SignInForm
 */
class Match extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('match');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set1Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set1Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set2Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set2Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set3Team1',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'set3Team2',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
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
            'type' => Element\Textarea::class,
            'name' => 'debrief',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Débriefing',
                'rows'      => 7,
                'required' => 'required',
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