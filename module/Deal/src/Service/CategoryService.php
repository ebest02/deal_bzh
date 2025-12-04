<?php

namespace Deal\Service;

use Deal\Model\Category;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class CategoryService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findAll(): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('categories')
            ->where(['is_active' => 1])
            ->order('name ASC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $categories = [];
        foreach ($result as $row) {
            $categories[] = $this->hydrateCategory($row);
        }

        return $categories;
    }

    public function findById(int $id): ?Category
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('categories')
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateCategory($result->current());
    }

    public function findBySlug(string $slug): ?Category
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('categories')
            ->where(['slug' => $slug, 'is_active' => 1]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateCategory($result->current());
    }

    protected function hydrateCategory(array $data): Category
    {
        $category = new Category();
        $category->setId((int) $data['id'])
            ->setName($data['name'])
            ->setSlug($data['slug'])
            ->setDescription($data['description'] ?? null)
            ->setParentId($data['parent_id'] ? (int) $data['parent_id'] : null)
            ->setIsActive((bool) $data['is_active'])
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null);

        return $category;
    }
}

