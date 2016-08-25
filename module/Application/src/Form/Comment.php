<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class Comment
 */
class Comment extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'comment',
            'options' => [
                'label' => 'Commentaire',
            ],
            'attributes' => [
                'placeholder' => 'Votre commentaire',
                'class' => 'form-control',
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