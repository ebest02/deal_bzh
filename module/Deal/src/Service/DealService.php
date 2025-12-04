<?php

namespace Deal\Service;

use Deal\Model\Deal;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;

class DealService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(int $id): ?Deal
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['d' => 'deals'])
            ->join(['u' => 'users'], 'd.user_id = u.id', ['user_email' => 'email', 'user_first_name' => 'first_name', 'user_last_name' => 'last_name'])
            ->join(['c' => 'categories'], 'd.category_id = c.id', ['category_name' => 'name', 'category_slug' => 'slug'])
            ->where(['d.id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateDeal($result->current());
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): Paginator
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['d' => 'deals'])
            ->join(['u' => 'users'], 'd.user_id = u.id', ['user_email' => 'email', 'user_first_name' => 'first_name', 'user_last_name' => 'last_name'])
            ->join(['c' => 'categories'], 'd.category_id = c.id', ['category_name' => 'name', 'category_slug' => 'slug'])
            ->where(['d.status' => 'published'])
            ->order('d.published_at DESC');

        $where = new Where();
        
        if (!empty($filters['category_id'])) {
            $where->equalTo('d.category_id', $filters['category_id']);
        }
        
        if (!empty($filters['type'])) {
            $where->equalTo('d.type', $filters['type']);
        }
        
        if (!empty($filters['search'])) {
            $where->nest()
                ->like('d.title', '%' . $filters['search'] . '%')
                ->or
                ->like('d.description', '%' . $filters['search'] . '%')
                ->unnest();
        }

        if (count($where->getPredicates()) > 0) {
            $select->where($where);
        }

        $adapter = new DbSelect($select, $this->adapter);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }

    public function findByUserId(int $userId): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['d' => 'deals'])
            ->join(['c' => 'categories'], 'd.category_id = c.id', ['category_name' => 'name', 'category_slug' => 'slug'])
            ->where(['d.user_id' => $userId])
            ->order('d.created_at DESC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $deals = [];
        foreach ($result as $row) {
            $deals[] = $this->hydrateDeal($row);
        }

        return $deals;
    }

    public function create(Deal $deal): Deal
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('deals');
        
        $data = [
            'user_id' => $deal->getUserId(),
            'category_id' => $deal->getCategoryId(),
            'title' => $deal->getTitle(),
            'description' => $deal->getDescription(),
            'type' => $deal->getType(),
            'status' => $deal->getStatus(),
            'location' => $deal->getLocation(),
            'price' => $deal->getPrice(),
            'is_negotiable' => $deal->getIsNegotiable() ? 1 : 0,
        ];

        if ($deal->getStatus() === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        $deal->setId($this->adapter->getDriver()->getLastGeneratedValue());
        return $deal;
    }

    public function update(Deal $deal): Deal
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('deals');

        $data = [
            'category_id' => $deal->getCategoryId(),
            'title' => $deal->getTitle(),
            'description' => $deal->getDescription(),
            'type' => $deal->getType(),
            'status' => $deal->getStatus(),
            'location' => $deal->getLocation(),
            'price' => $deal->getPrice(),
            'is_negotiable' => $deal->getIsNegotiable() ? 1 : 0,
        ];

        if ($deal->getStatus() === 'published' && !$deal->getPublishedAt()) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $update->set($data)
            ->where(['id' => $deal->getId()]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();

        return $deal;
    }

    public function delete(int $id): bool
    {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete('deals')
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();

        return $result->getAffectedRows() > 0;
    }

    public function incrementViews(int $id): void
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('deals')
            ->set(['views' => new \Laminas\Db\Sql\Expression('views + 1')])
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    protected function hydrateDeal(array $data): Deal
    {
        $deal = new Deal();
        $deal->setId((int) $data['id'])
            ->setUserId((int) $data['user_id'])
            ->setCategoryId((int) $data['category_id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setType($data['type'])
            ->setStatus($data['status'])
            ->setLocation($data['location'])
            ->setPrice($data['price'] ? (float) $data['price'] : null)
            ->setIsNegotiable((bool) $data['is_negotiable'])
            ->setViews((int) ($data['views'] ?? 0))
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null)
            ->setPublishedAt($data['published_at'] ?? null);

        if (isset($data['category_name'])) {
            $category = new \Deal\Model\Category();
            $category->setId((int) $data['category_id'])
                ->setName($data['category_name'])
                ->setSlug($data['category_slug'] ?? '');
            $deal->setCategory($category);
        }

        if (isset($data['user_email'])) {
            $user = new \User\Model\User();
            $user->setId((int) $data['user_id'])
                ->setEmail($data['user_email'])
                ->setFirstName($data['user_first_name'] ?? null)
                ->setLastName($data['user_last_name'] ?? null);
            $deal->setUser($user);
        }

        return $deal;
    }
}

