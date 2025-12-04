<?php

namespace User\Service;

use User\Model\User;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

class UserService
{
    protected $adapter;
    protected $hydrator;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->hydrator = new ClassMethodsHydrator();
    }

    public function findById(int $id): ?User
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('users')
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateUser($result->current());
    }

    public function findByEmail(string $email): ?User
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('users')
            ->where(['email' => $email]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateUser($result->current());
    }

    public function create(User $user): User
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('users');
        
        $data = [
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole(),
            'is_active' => $user->getIsActive() ? 1 : 0,
        ];

        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        $user->setId($this->adapter->getDriver()->getLastGeneratedValue());
        return $user;
    }

    public function update(User $user): User
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('users');

        $data = [
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole(),
            'is_active' => $user->getIsActive() ? 1 : 0,
        ];

        if ($user->getPassword()) {
            $data['password'] = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        }

        $update->set($data)
            ->where(['id' => $user->getId()]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();

        return $user;
    }

    protected function hydrateUser(array $data): User
    {
        $user = new User();
        $user->setId((int) $data['id'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setPhone($data['phone'])
            ->setRole($data['role'])
            ->setRating((float) ($data['rating'] ?? 0))
            ->setRatingCount((int) ($data['rating_count'] ?? 0))
            ->setIsActive((bool) $data['is_active'])
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null);

        return $user;
    }
}

