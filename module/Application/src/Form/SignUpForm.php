<?php
/**
 * Created by PhpStorm.
 * User: semihs
 * Date: 21.07.2016
 * Time: 20:57
 */

namespace Authentication\Form;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class SignUpForm
 */
class SignUpForm extends Form
{
    /**
     * @var CaptchaAdapter
     */
    protected $captcha;
    /**
     * @var Translator
     */
    protected $translator;

    public function init()
    {
        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => $this->getTranslator()->translate('Name'),
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('Name'),
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'username',
            'options' => [
                'label' => $this->getTranslator()->translate('Username'),
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('Username'),
            ],
        ]);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => $this->getTranslator()->translate('E-mail'),
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('E-mail'),
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'password',
            'options' => [
                'label' => $this->getTranslator()->translate('Password'),
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('Password'),
            ],
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 're_password',
            'options' => [
                'label' => $this->getTranslator()->translate('Password (Re-type)'),
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('Password (Re-type)'),
            ],
        ]);

        $this->add([
            'type' => Element\Captcha::class,
            'name' => 'captcha',
            'options' => [
                'label' => $this->getTranslator()->translate('Robot Değilim'),
                'captcha' => $this->captcha,
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => $this->getTranslator()->translate('Robot Değilim'),
            ],
        ]);
        $this->add(new Element\Csrf('security'));
        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => $this->getTranslator()->translate('Sign Up'),
                'class' => 'btn btn-primary btn-block',
            ],
        ]);
    }

    /**
     * @return CaptchaAdapter
     */
    public function getCaptcha()
    {
        return $this->captcha;
    }

    /**
     * @param CaptchaAdapter $captcha
     */
    public function setCaptcha(CaptchaAdapter $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}