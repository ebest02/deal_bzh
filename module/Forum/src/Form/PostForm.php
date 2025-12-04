<?php

namespace Forum\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class PostForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('post', $options);

        $this->add([
            'name' => 'content',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Votre message',
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
                'value' => 'Répondre',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->setInputFilter($this->createInputFilter());
    }

    protected function createInputFilter(): InputFilter
    {
        $inputFilter = new InputFilter();

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

