<?php

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Service\AuthService;
use User\Service\UserService;

class ProfileController extends AbstractActionController
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function indexAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $user = $this->authService->getIdentity();
        return new ViewModel([
            'user' => $user,
        ]);
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        
        if (!$id) {
            if (!$this->authService->hasIdentity()) {
                return $this->redirect()->toRoute('user/login');
            }
            $user = $this->authService->getIdentity();
        } else {
            $user = $this->userService->findById($id);
            if (!$user) {
                return $this->notFoundAction();
            }
        }

        return new ViewModel([
            'user' => $user,
        ]);
    }

    public function editAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $user = $this->authService->getIdentity();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            
            $user->setFirstName($data['first_name'] ?? null)
                ->setLastName($data['last_name'] ?? null)
                ->setPhone($data['phone'] ?? null);

            if (!empty($data['password'])) {
                $user->setPassword($data['password']);
            }

            $this->userService->update($user);
            $this->flashMessenger()->addSuccessMessage('Profil mis à jour avec succès.');
            return $this->redirect()->toRoute('user/profile');
        }

        return new ViewModel([
            'user' => $user,
        ]);
    }
}

