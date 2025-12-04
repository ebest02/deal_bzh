<?php

namespace Deal\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Deal\Service\DealService;
use Deal\Service\CategoryService;
use Deal\Service\FavoriteService;
use Deal\Form\DealForm;
use Deal\Model\Deal;
use User\Service\AuthService;

class DealController extends AbstractActionController
{
    protected $dealService;
    protected $categoryService;
    protected $favoriteService;
    protected $authService;

    public function __construct(
        DealService $dealService,
        CategoryService $categoryService,
        FavoriteService $favoriteService,
        AuthService $authService
    ) {
        $this->dealService = $dealService;
        $this->categoryService = $categoryService;
        $this->favoriteService = $favoriteService;
        $this->authService = $authService;
    }

    public function indexAction(): ViewModel
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $filters = [
            'category_id' => $this->params()->fromQuery('category_id'),
            'type' => $this->params()->fromQuery('type'),
            'search' => $this->params()->fromQuery('search'),
        ];

        $paginator = $this->dealService->findAll($filters, $page);
        $categories = $this->categoryService->findAll();

        return new ViewModel([
            'paginator' => $paginator,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal || !$deal->isPublished()) {
            return $this->notFoundAction();
        }

        $this->dealService->incrementViews($id);

        $isFavorite = false;
        if ($this->authService->hasIdentity()) {
            $isFavorite = $this->favoriteService->isFavorite(
                $this->authService->getIdentity()->getId(),
                $id
            );
        }

        return new ViewModel([
            'deal' => $deal,
            'isFavorite' => $isFavorite,
        ]);
    }

    public function createAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $categories = $this->categoryService->findAll();
        $form = new DealForm('deal', ['categories' => $categories]);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $deal = new Deal();
                $deal->setUserId($this->authService->getIdentity()->getId())
                    ->setCategoryId((int) $data['category_id'])
                    ->setTitle($data['title'])
                    ->setDescription($data['description'])
                    ->setType($data['type'])
                    ->setLocation($data['location'] ?? null)
                    ->setPrice($data['price'] ? (float) $data['price'] : null)
                    ->setIsNegotiable((bool) ($data['is_negotiable'] ?? false))
                    ->setStatus($data['status'] ?? 'draft');

                $this->dealService->create($deal);
                $this->flashMessenger()->addSuccessMessage('Annonce créée avec succès.');
                return $this->redirect()->toRoute('deal/view', ['id' => $deal->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal || $deal->getUserId() !== $this->authService->getIdentity()->getId()) {
            return $this->notFoundAction();
        }

        $categories = $this->categoryService->findAll();
        $form = new DealForm('deal', ['categories' => $categories]);
        $form->setData([
            'title' => $deal->getTitle(),
            'description' => $deal->getDescription(),
            'category_id' => $deal->getCategoryId(),
            'type' => $deal->getType(),
            'location' => $deal->getLocation(),
            'price' => $deal->getPrice(),
            'is_negotiable' => $deal->getIsNegotiable(),
            'status' => $deal->getStatus(),
        ]);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $deal->setCategoryId((int) $data['category_id'])
                    ->setTitle($data['title'])
                    ->setDescription($data['description'])
                    ->setType($data['type'])
                    ->setLocation($data['location'] ?? null)
                    ->setPrice($data['price'] ? (float) $data['price'] : null)
                    ->setIsNegotiable((bool) ($data['is_negotiable'] ?? false))
                    ->setStatus($data['status']);

                $this->dealService->update($deal);
                $this->flashMessenger()->addSuccessMessage('Annonce mise à jour avec succès.');
                return $this->redirect()->toRoute('deal/view', ['id' => $deal->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form,
            'deal' => $deal,
        ]);
    }

    public function deleteAction()
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal || $deal->getUserId() !== $this->authService->getIdentity()->getId()) {
            return $this->notFoundAction();
        }

        $this->dealService->delete($id);
        $this->flashMessenger()->addSuccessMessage('Annonce supprimée avec succès.');
        return $this->redirect()->toRoute('deal');
    }

    public function favoriteAction()
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal) {
            return $this->notFoundAction();
        }

        $userId = $this->authService->getIdentity()->getId();

        if ($this->favoriteService->isFavorite($userId, $id)) {
            $this->favoriteService->removeFavorite($userId, $id);
            $this->flashMessenger()->addSuccessMessage('Retiré des favoris.');
        } else {
            $this->favoriteService->addFavorite($userId, $id);
            $this->flashMessenger()->addSuccessMessage('Ajouté aux favoris.');
        }

        return $this->redirect()->toRoute('deal/view', ['id' => $id]);
    }
}

