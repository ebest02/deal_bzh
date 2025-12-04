<?php

namespace Forum\Service;

use Forum\Model\ForumCategory;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class ForumService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findAllCategories(): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('forum_categories')
            ->where(['is_active' => 1])
            ->order('order ASC, name ASC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $categories = [];
        foreach ($result as $row) {
            $categories[] = $this->hydrateCategory($row);
        }

        return $categories;
    }

    public function findCategoryBySlug(string $slug): ?ForumCategory
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('forum_categories')
            ->where(['slug' => $slug, 'is_active' => 1]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateCategory($result->current());
    }

    public function findCategoryById(int $id): ?ForumCategory
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('forum_categories')
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateCategory($result->current());
    }

    protected function hydrateCategory(array $data): ForumCategory
    {
        $category = new ForumCategory();
        $category->setId((int) $data['id'])
            ->setName($data['name'])
            ->setSlug($data['slug'])
            ->setDescription($data['description'] ?? null)
            ->setOrder((int) ($data['order'] ?? 0))
            ->setIsActive((bool) $data['is_active'])
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null);

        return $category;
    }
}

