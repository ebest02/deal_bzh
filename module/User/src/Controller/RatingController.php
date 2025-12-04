<?php

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use User\Service\RatingService;
use User\Service\AuthService;
use Deal\Service\DealService;

class RatingController extends AbstractActionController
{
    protected $ratingService;
    protected $authService;
    protected $dealService;

    public function __construct(
        RatingService $ratingService,
        AuthService $authService,
        DealService $dealService
    ) {
        $this->ratingService = $ratingService;
        $this->authService = $authService;
        $this->dealService = $dealService;
    }

    public function createAction()
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            
            $dealId = (int) ($data['deal_id'] ?? 0);
            $deal = $this->dealService->findById($dealId);

            if (!$deal) {
                $this->flashMessenger()->addErrorMessage('Annonce introuvable.');
                return $this->redirect()->toRoute('deal');
            }

            $raterId = $this->authService->getIdentity()->getId();
            $ratedUserId = $deal->getUserId();

            if ($raterId === $ratedUserId) {
                $this->flashMessenger()->addErrorMessage('Vous ne pouvez pas vous noter vous-même.');
                return $this->redirect()->toRoute('deal/view', ['id' => $dealId]);
            }

            $score = (int) ($data['score'] ?? 0);
            if ($score < 1 || $score > 5) {
                $this->flashMessenger()->addErrorMessage('La note doit être entre 1 et 5.');
                return $this->redirect()->toRoute('deal/view', ['id' => $dealId]);
            }

            $comment = $data['comment'] ?? null;

            $this->ratingService->createRating($raterId, $ratedUserId, $dealId, $score, $comment);
            $this->flashMessenger()->addSuccessMessage('Note enregistrée avec succès.');
        }

        return $this->redirect()->toRoute('deal/view', ['id' => $dealId]);
    }
}

