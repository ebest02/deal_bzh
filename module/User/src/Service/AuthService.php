<?php

namespace User\Service;

use User\Model\User;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Db\Adapter\AdapterInterface;

class AuthService
{
    protected $authService;
    protected $adapter;
    protected $userService;

    public function __construct(
        AuthenticationService $authService,
        AdapterInterface $adapter,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->adapter = $adapter;
        $this->userService = $userService;
    }

    public function authenticate(string $email, string $password): bool
    {
        $authAdapter = new CredentialTreatmentAdapter(
            $this->adapter,
            'users',
            'email',
            'password'
        );

        $authAdapter->setIdentity($email)
            ->setCredential($password);

        $result = $this->authService->authenticate($authAdapter);

        if ($result->isValid()) {
            $user = $this->userService->findByEmail($email);
            if ($user && $user->getIsActive()) {
                $this->authService->getStorage()->write($user);
                return true;
            }
        }

        return false;
    }

    public function logout(): void
    {
        $this->authService->clearIdentity();
    }

    public function getIdentity(): ?User
    {
        if (!$this->authService->hasIdentity()) {
            return null;
        }

        $identity = $this->authService->getIdentity();
        if ($identity instanceof User) {
            return $identity;
        }

        // Si c'est juste l'email, récupérer l'utilisateur complet
        if (is_string($identity)) {
            return $this->userService->findByEmail($identity);
        }

        return null;
    }

    public function hasIdentity(): bool
    {
        return $this->authService->hasIdentity();
    }
}

