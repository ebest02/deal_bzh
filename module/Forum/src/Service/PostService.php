<?php

namespace Forum\Service;

use Forum\Model\Post;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Adapter\ArrayAdapter;

class PostService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findByTopicId(int $topicId, int $page = 1, int $perPage = 20): Paginator
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['p' => 'forum_posts'])
            ->join(['u' => 'users'], 'p.user_id = u.id', ['user_email' => 'email', 'user_first_name' => 'first_name', 'user_last_name' => 'last_name'])
            ->where(['p.topic_id' => $topicId, 'p.status' => 'published'])
            ->order('p.created_at ASC');

        $adapter = new DbSelect($select, $this->adapter);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        // Hydrater les posts avec les utilisateurs
        $posts = [];
        foreach ($paginator as $row) {
            $posts[] = $this->hydratePost($row);
        }
        
        // Créer un adaptateur personnalisé pour retourner les posts hydratés
        $adapter = new ArrayAdapter($posts);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }

    public function create(Post $post): Post
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('forum_posts');
        
        $data = [
            'topic_id' => $post->getTopicId(),
            'user_id' => $post->getUserId(),
            'content' => $post->getContent(),
            'is_first_post' => $post->getIsFirstPost() ? 1 : 0,
            'status' => $post->getStatus(),
        ];

        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        $post->setId($this->adapter->getDriver()->getLastGeneratedValue());

        // Mettre à jour le topic (dernier message, compteur)
        $this->updateTopicAfterPost($post->getTopicId(), $post->getUserId());

        return $post;
    }

    protected function updateTopicAfterPost(int $topicId, int $userId): void
    {
        $sql = new Sql($this->adapter);
        
        // Compter les réponses
        $select = $sql->select('forum_posts')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')])
            ->where(['topic_id' => $topicId, 'status' => 'published', 'is_first_post' => 0]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $repliesCount = (int) $result->current()['count'];

        // Mettre à jour le topic
        $update = $sql->update('forum_topics')
            ->set([
                'replies_count' => $repliesCount,
                'last_reply_at' => date('Y-m-d H:i:s'),
                'last_reply_user_id' => $userId,
            ])
            ->where(['id' => $topicId]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    protected function hydratePost(array $data): Post
    {
        $post = new Post();
        $post->setId((int) $data['id'])
            ->setTopicId((int) $data['topic_id'])
            ->setUserId((int) $data['user_id'])
            ->setContent($data['content'])
            ->setIsFirstPost((bool) $data['is_first_post'])
            ->setStatus($data['status'])
            ->setCreatedAt($data['created_at'])
            ->setUpdatedAt($data['updated_at'] ?? null);

        if (isset($data['user_email'])) {
            $user = new \User\Model\User();
            $user->setId((int) $data['user_id'])
                ->setEmail($data['user_email'])
                ->setFirstName($data['user_first_name'] ?? null)
                ->setLastName($data['user_last_name'] ?? null);
            $post->setUser($user);
        }

        return $post;
    }
}

