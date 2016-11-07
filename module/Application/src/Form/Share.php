<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class Share
 */
class Share extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'name' => 'emails',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Adresses email',
            ],
            'attributes' => [
                'class' => 'form-control',
                'style' => 'height: 40px;',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Envoyer',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}