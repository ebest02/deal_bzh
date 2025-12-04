<?php

namespace Forum\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class TopicForm extends Form
{
    protected $categories = [];

    public function __construct($name = null, array $options = [])
    {
        parent::__construct('topic', $options);

        if (isset($options['categories'])) {
            $this->categories = $options['categories'];
        }

        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Titre',
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
                'rows' => 15,
            ],
        ]);

        $this->add([
            'name' => 'category_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Catégorie',
                'value_options' => $this->getCategoryOptions(),
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
                'value' => 'Créer le sujet',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->setInputFilter($this->createInputFilter());
    }

    protected function getCategoryOptions(): array
    {
        $options = ['' => '-- Sélectionner une catégorie --'];
        foreach ($this->categories as $category) {
            $options[$category->getId()] = $category->getName();
        }
        return $options;
    }

    protected function createInputFilter(): InputFilter
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'title',
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

        $inputFilter->add([
            'name' => 'category_id',
            'required' => true,
            'validators' => [
                ['name' => 'Digits'],
            ],
        ]);

        return $inputFilter;
    }
}

