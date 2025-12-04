<?php

namespace Message\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class MessageForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('message', $options);

        $this->add([
            'name' => 'to_user_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'deal_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'subject',
            'type' => 'Text',
            'options' => [
                'label' => 'Sujet',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'content',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Message',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'rows' => 10,
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
                'value' => 'Envoyer',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->setInputFilter($this->createInputFilter());
    }

    protected function createInputFilter(): InputFilter
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'to_user_id',
            'required' => true,
            'validators' => [
                ['name' => 'Digits'],
            ],
        ]);

        $inputFilter->add([
            'name' => 'deal_id',
            'required' => false,
            'validators' => [
                ['name' => 'Digits'],
            ],
        ]);

        $inputFilter->add([
            'name' => 'subject',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['min' => 3, 'max' => 255]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'content',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['min' => 10]],
            ],
        ]);

        return $inputFilter;
    }
}

