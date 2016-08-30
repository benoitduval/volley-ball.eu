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
                'placeholder' => 'Nom du groupe',
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
                'placeholder' => 'Description du club, équipes etc',
                'class' => 'form-control',
            ],
        ]);


        $this->add([
            'name' => 'info',
            'type' => Element\Textarea::class,
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Information concernant les séances (niveau, jour, horaire ...)',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'enable',
            'options' => [
                'label' => 'Enable',
                'use_hidden_element' => false,
                'unchecked_value' => 'off',
                'checked_value' => 'on',
            ],
            'attributes' => [
                'value' => 'on'
            ]
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'showUsers',
            'options' => [
                'label' => 'showUsers',
                'use_hidden_element' => false,
                'unchecked_value' => 'off',
                'checked_value' => 'on',
            ],
            'attributes' => [
                'value' => 'on'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Connexion',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    public function isValid()
    {
        $data = $this->data;
        if (!isset($data['weather'])) {
            $data['weather'] = 'off';
            $this->data = $data;
        } else {
            $data['weather'] = 'on';
        }

        if (!isset($data['showUsers'])) {
            $data['showUsers'] = 'off';
            $this->data = $data;
        } else {
            $data['showUsers'] = 'on';
        }

        if (!isset($data['enable'])) {
            $data['enable'] = 'off';
            $this->data = $data;
        } else {
            $data['enable'] = 'on';
        }

        return parent::isValid();
    }
}