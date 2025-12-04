<?php

namespace Deal\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class DealForm extends Form
{
    protected $categories = [];

    public function __construct($name = null, array $options = [])
    {
        parent::__construct('deal', $options);

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
            'name' => 'description',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'rows' => 10,
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
            'name' => 'type',
            'type' => 'Select',
            'options' => [
                'label' => 'Type d\'annonce',
                'value_options' => [
                    'exchange' => 'Échange',
                    'swap' => 'Troc',
                    'free_service' => 'Service gratuit',
                    'sale' => 'Vente',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'location',
            'type' => 'Text',
            'options' => [
                'label' => 'Localisation',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'price',
            'type' => 'Number',
            'options' => [
                'label' => 'Prix (€)',
            ],
            'attributes' => [
                'class' => 'form-control',
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        $this->add([
            'name' => 'is_negotiable',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Prix négociable',
            ],
        ]);

        $this->add([
            'name' => 'status',
            'type' => 'Select',
            'options' => [
                'label' => 'Statut',
                'value_options' => [
                    'draft' => 'Brouillon',
                    'published' => 'Publié',
                ],
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
                'value' => 'Enregistrer',
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
            'name' => 'description',
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

        $inputFilter->add([
            'name' => 'type',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'location',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['max' => 255]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'price',
            'required' => false,
            'validators' => [
                ['name' => 'GreaterThan', 'options' => ['min' => 0]],
            ],
        ]);

        $inputFilter->add([
            'name' => 'is_negotiable',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'status',
            'required' => false,
        ]);

        return $inputFilter;
    }
}

