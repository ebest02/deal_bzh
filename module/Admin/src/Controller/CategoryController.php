<?php

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Deal\Service\CategoryService;

class CategoryController extends AbstractActionController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function indexAction(): ViewModel
    {
        $categories = $this->categoryService->findAll();

        return new ViewModel([
            'categories' => $categories,
        ]);
    }
}

