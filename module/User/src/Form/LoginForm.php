<?php

namespace User\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('login', $options);

        $this->add([
            'name' => 'email',
            'type' => 'Email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Mot de passe',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'csrf',
            'type' => 'Csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Se connecter',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->setInputFilter($this->createInputFilter());
    }

    protected function createInputFilter(): InputFilter
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToLower'],
            ],
            'validators' => [
                ['name' => 'EmailAddress'],
            ],
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);

        return $inputFilter;
    }
}

