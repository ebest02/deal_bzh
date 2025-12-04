<?php

namespace Forum\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Forum\Service\TopicService;
use Forum\Service\PostService;
use Forum\Service\ForumService;
use Forum\Form\TopicForm;
use Forum\Form\PostForm;
use Forum\Model\Topic;
use Forum\Model\Post;
use User\Service\AuthService;

class TopicController extends AbstractActionController
{
    protected $topicService;
    protected $postService;
    protected $forumService;
    protected $authService;

    public function __construct(
        TopicService $topicService,
        PostService $postService,
        ForumService $forumService,
        AuthService $authService
    ) {
        $this->topicService = $topicService;
        $this->postService = $postService;
        $this->forumService = $forumService;
        $this->authService = $authService;
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $topic = $this->topicService->findById($id);

        if (!$topic || $topic->getStatus() !== 'published') {
            return $this->notFoundAction();
        }

        $this->topicService->incrementViews($id);

        $page = (int) $this->params()->fromQuery('page', 1);
        $paginator = $this->postService->findByTopicId($id, $page);

        return new ViewModel([
            'topic' => $topic,
            'paginator' => $paginator,
        ]);
    }

    public function createAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $categories = $this->forumService->findAllCategories();
        $form = new TopicForm('topic', ['categories' => $categories]);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $topic = new Topic();
                $topic->setCategoryId((int) $data['category_id'])
                    ->setUserId($this->authService->getIdentity()->getId())
                    ->setTitle($data['title'])
                    ->setContent($data['content'])
                    ->setStatus('published');

                $this->topicService->create($topic);

                // Créer le premier message
                $post = new Post();
                $post->setTopicId($topic->getId())
                    ->setUserId($topic->getUserId())
                    ->setContent($data['content'])
                    ->setIsFirstPost(true)
                    ->setStatus('published');

                $this->postService->create($post);

                $this->flashMessenger()->addSuccessMessage('Sujet créé avec succès.');
                return $this->redirect()->toRoute('forum/topic', ['id' => $topic->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function replyAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $topicId = (int) $this->params()->fromRoute('topicId');
        $topic = $this->topicService->findById($topicId);

        if (!$topic || $topic->getIsLocked()) {
            return $this->notFoundAction();
        }

        $form = new PostForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $post = new Post();
                $post->setTopicId($topicId)
                    ->setUserId($this->authService->getIdentity()->getId())
                    ->setContent($data['content'])
                    ->setIsFirstPost(false)
                    ->setStatus('published');

                $this->postService->create($post);
                $this->flashMessenger()->addSuccessMessage('Réponse ajoutée avec succès.');
                return $this->redirect()->toRoute('forum/topic', ['id' => $topicId]);
            }
        }

        return new ViewModel([
            'form' => $form,
            'topic' => $topic,
        ]);
    }
}

