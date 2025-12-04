<?php

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Deal\Service\DealService;

class DealController extends AbstractActionController
{
    protected $dealService;

    public function __construct(DealService $dealService)
    {
        $this->dealService = $dealService;
    }

    public function indexAction(): ViewModel
    {
        $status = $this->params()->fromQuery('status', 'all');
        $filters = [];

        if ($status !== 'all') {
            $filters['status'] = $status;
        }

        $page = (int) $this->params()->fromQuery('page', 1);
        $paginator = $this->dealService->findAll($filters, $page, 50);

        return new ViewModel([
            'paginator' => $paginator,
            'status' => $status,
        ]);
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'deal' => $deal,
        ]);
    }

    public function moderateAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $deal = $this->dealService->findById($id);

        if (!$deal) {
            return $this->notFoundAction();
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $action = $request->getPost('action');
            
            switch ($action) {
                case 'approve':
                    $deal->setStatus('published');
                    if (!$deal->getPublishedAt()) {
                        $deal->setPublishedAt(new \DateTime());
                    }
                    $this->dealService->update($deal);
                    $this->flashMessenger()->addSuccessMessage('Annonce approuvée.');
                    break;
                case 'reject':
                    $deal->setStatus('rejected');
                    $this->dealService->update($deal);
                    $this->flashMessenger()->addSuccessMessage('Annonce rejetée.');
                    break;
                case 'archive':
                    $deal->setStatus('archived');
                    $this->dealService->update($deal);
                    $this->flashMessenger()->addSuccessMessage('Annonce archivée.');
                    break;
            }

            return $this->redirect()->toRoute('admin/deals');
        }

        return new ViewModel([
            'deal' => $deal,
        ]);
    }
}

