<?php

namespace User\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class RegisterForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('register', $options);

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
            'name' => 'password_confirm',
            'type' => 'Password',
            'options' => [
                'label' => 'Confirmer le mot de passe',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'first_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Prénom',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'last_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Nom',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'phone',
            'type' => 'Text',
            'options' => [
                'label' => 'Téléphone',
            ],
            'attributes' => [
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
                'value' => 'S\'inscrire',
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
                ['name' => 'StringLength', 'options' => ['max' => 255]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'validators' => [
                ['name' => 'StringLength', 'options' => ['min' => 6]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'password_confirm',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'first_name',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['max' => 100]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'last_name',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['max' => 100]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'phone',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['max' => 20]],
            ],
        ]);

        return $inputFilter;
    }
}

