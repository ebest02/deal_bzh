<?php

namespace Deal\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Deal\Service\DealService;
use Deal\Service\CategoryService;

class CategoryController extends AbstractActionController
{
    protected $dealService;
    protected $categoryService;

    public function __construct(DealService $dealService, CategoryService $categoryService)
    {
        $this->dealService = $dealService;
        $this->categoryService = $categoryService;
    }

    public function viewAction(): ViewModel
    {
        $slug = $this->params()->fromRoute('slug');
        $category = $this->categoryService->findBySlug($slug);

        if (!$category) {
            return $this->notFoundAction();
        }

        $page = (int) $this->params()->fromQuery('page', 1);
        $filters = [
            'category_id' => $category->getId(),
            'type' => $this->params()->fromQuery('type'),
            'search' => $this->params()->fromQuery('search'),
        ];

        $paginator = $this->dealService->findAll($filters, $page);
        $categories = $this->categoryService->findAll();

        return new ViewModel([
            'category' => $category,
            'paginator' => $paginator,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }
}

