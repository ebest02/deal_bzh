<?php

namespace Forum\Service;

use Forum\Model\Topic;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Adapter\ArrayAdapter;

class TopicService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(int $id): ?Topic
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['t' => 'forum_topics'])
            ->join(['u' => 'users'], 't.user_id = u.id', ['user_email' => 'email', 'user_first_name' => 'first_name', 'user_last_name' => 'last_name'])
            ->join(['c' => 'forum_categories'], 't.category_id = c.id', ['category_name' => 'name', 'category_slug' => 'slug'])
            ->where(['t.id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateTopic($result->current());
    }

    public function findByCategory(int $categoryId, int $page = 1, int $perPage = 20): Paginator
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['t' => 'forum_topics'])
            ->join(['u' => 'users'], 't.user_id = u.id', ['user_email' => 'email', 'user_first_name' => 'first_name', 'user_last_name' => 'last_name'])
            ->joinLeft(['lr' => 'users'], 't.last_reply_user_id = lr.id', ['last_reply_email' => 'email', 'last_reply_first_name' => 'first_name', 'last_reply_last_name' => 'last_name'])
            ->where(['t.category_id' => $categoryId, 't.status' => 'published'])
            ->order(['t.is_pinned DESC', 't.last_reply_at DESC', 't.created_at DESC']);

        $adapter = new DbSelect($select, $this->adapter);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        // Hydrater les topics avec les utilisateurs
        $topics = [];
        foreach ($paginator as $row) {
            $topics[] = $this->hydrateTopic($row);
        }
        
        // Créer un adaptateur personnalisé pour retourner les topics hydratés
        $adapter = new ArrayAdapter($topics);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }

    public function create(Topic $topic): Topic
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('forum_topics');
        
        $data = [
            'category_id' => $topic->getCategoryId(),
            'user_id' => $topic->getUserId(),
            'title' => $topic->getTitle(),
            'content' => $topic->getContent(),
            'is_pinned' => $topic->getIsPinned() ? 1 : 0,
            'is_locked' => $topic->getIsLocked() ? 1 : 0,
            'status' => $topic->getStatus(),
            'replies_count' => 0,
        ];

        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        $topic->setId($this->adapter->getDriver()->getLastGeneratedValue());
        return $topic;
    }

    public function incrementViews(int $id): void
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('forum_topics')
            ->set(['views' => new \Laminas\Db\Sql\Expression('views + 1')])
            ->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    protected function hydrateTopic(array $data): Topic
    {
        $topic = new Topic();
        $topic->setId((int) $data['id'])
            ->setCategoryId((int) $data['category_id'])
            ->setUserId((int) $data['user_id'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setIsPinned((bool) $data['is_pinned'])
            ->setIsLocked((bool) $data['is_locked'])
            ->setViews((int) ($data['views'] ?? 0))
            ->setRepliesCount((int) ($data['replies_count'] ?? 0))
            ->setLastReplyAt($data['last_reply_at'] ?? null)
            ->setLastReplyUserId($data['last_reply_user_id'] ? (int) $data['last_reply_user_id'] : null)
            ->setStatus($data['status'])
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null);

        if (isset($data['category_name'])) {
            $category = new \Forum\Model\ForumCategory();
            $category->setId((int) $data['category_id'])
                ->setName($data['category_name'])
                ->setSlug($data['category_slug'] ?? '');
            $topic->setCategory($category);
        }

        if (isset($data['user_email'])) {
            $user = new \User\Model\User();
            $user->setId((int) $data['user_id'])
                ->setEmail($data['user_email'])
                ->setFirstName($data['user_first_name'] ?? null)
                ->setLastName($data['user_last_name'] ?? null);
            $topic->setUser($user);
        }

        if (isset($data['last_reply_email'])) {
            $lastReplyUser = new \User\Model\User();
            $lastReplyUser->setId((int) $data['last_reply_user_id'])
                ->setEmail($data['last_reply_email'])
                ->setFirstName($data['last_reply_first_name'] ?? null)
                ->setLastName($data['last_reply_last_name'] ?? null);
            $topic->setLastReplyUser($lastReplyUser);
        }

        return $topic;
    }
}

