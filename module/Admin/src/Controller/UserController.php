<?php

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Service\UserService;
use Laminas\Db\Adapter\AdapterInterface;

class UserController extends AbstractActionController
{
    protected $userService;
    protected $adapter;

    public function __construct(UserService $userService, AdapterInterface $adapter)
    {
        $this->userService = $userService;
        $this->adapter = $adapter;
    }

    public function indexAction(): ViewModel
    {
        $sql = new \Laminas\Db\Sql\Sql($this->adapter);
        $select = $sql->select('users')
            ->order('created_at DESC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $users = [];
        foreach ($result as $row) {
            $users[] = $this->userService->findById((int) $row['id']);
        }

        return new ViewModel([
            'users' => $users,
        ]);
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'user' => $user,
        ]);
    }

    public function editAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->notFoundAction();
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            
            $user->setFirstName($data['first_name'] ?? null)
                ->setLastName($data['last_name'] ?? null)
                ->setPhone($data['phone'] ?? null)
                ->setRole($data['role'] ?? 'user')
                ->setIsActive((bool) ($data['is_active'] ?? true));

            if (!empty($data['password'])) {
                $user->setPassword($data['password']);
            }

            $this->userService->update($user);
            $this->flashMessenger()->addSuccessMessage('Utilisateur mis à jour avec succès.');
            return $this->redirect()->toRoute('admin/users/view', ['id' => $id]);
        }

        return new ViewModel([
            'user' => $user,
        ]);
    }
}

