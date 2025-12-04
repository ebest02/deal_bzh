<?php

namespace Forum\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Forum\Service\ForumService;
use Forum\Service\TopicService;

class ForumController extends AbstractActionController
{
    protected $forumService;
    protected $topicService;

    public function __construct(ForumService $forumService, TopicService $topicService)
    {
        $this->forumService = $forumService;
        $this->topicService = $topicService;
    }

    public function indexAction(): ViewModel
    {
        $categories = $this->forumService->findAllCategories();
        return new ViewModel([
            'categories' => $categories,
        ]);
    }

    public function categoryAction(): ViewModel
    {
        $slug = $this->params()->fromRoute('slug');
        $category = $this->forumService->findCategoryBySlug($slug);

        if (!$category) {
            return $this->notFoundAction();
        }

        $page = (int) $this->params()->fromQuery('page', 1);
        $paginator = $this->topicService->findByCategory($category->getId(), $page);

        return new ViewModel([
            'category' => $category,
            'paginator' => $paginator,
        ]);
    }
}

