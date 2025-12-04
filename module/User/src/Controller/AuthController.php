<?php

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\LoginForm;
use User\Form\RegisterForm;
use User\Service\AuthService;
use User\Service\UserService;
use User\Model\User;

class AuthController extends AbstractActionController
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function loginAction(): ViewModel
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $form = new LoginForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                if ($this->authService->authenticate($data['email'], $data['password'])) {
                    return $this->redirect()->toRoute('home');
                } else {
                    $this->flashMessenger()->addErrorMessage('Email ou mot de passe incorrect.');
                }
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function registerAction(): ViewModel
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $form = new RegisterForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                // Vérifier si l'email existe déjà
                if ($this->userService->findByEmail($data['email'])) {
                    $this->flashMessenger()->addErrorMessage('Cet email est déjà utilisé.');
                } else {
                    $user = new User();
                    $user->setEmail($data['email'])
                        ->setPassword($data['password'])
                        ->setFirstName($data['first_name'])
                        ->setLastName($data['last_name'])
                        ->setPhone($data['phone'])
                        ->setRole('user')
                        ->setIsActive(true);

                    $this->userService->create($user);
                    $this->flashMessenger()->addSuccessMessage('Inscription réussie ! Vous pouvez maintenant vous connecter.');
                    return $this->redirect()->toRoute('user/login');
                }
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function logoutAction()
    {
        $this->authService->logout();
        $this->flashMessenger()->addSuccessMessage('Vous avez été déconnecté.');
        return $this->redirect()->toRoute('home');
    }
}

