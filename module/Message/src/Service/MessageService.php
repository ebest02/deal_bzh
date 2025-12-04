<?php

namespace Message\Service;

use Message\Model\Message;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class MessageService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    public function findById(int $id): ?Message
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['m' => 'messages'])
            ->join(['fu' => 'users'], 'm.from_user_id = fu.id', ['from_email' => 'email', 'from_first_name' => 'first_name', 'from_last_name' => 'last_name'])
            ->join(['tu' => 'users'], 'm.to_user_id = tu.id', ['to_email' => 'email', 'to_first_name' => 'first_name', 'to_last_name' => 'last_name'])
            ->where(['m.id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result->count() === 0) {
            return null;
        }

        return $this->hydrateMessage($result->current());
    }

    public function findByUserId(int $userId, string $type = 'received'): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['m' => 'messages'])
            ->join(['fu' => 'users'], 'm.from_user_id = fu.id', ['from_email' => 'email', 'from_first_name' => 'first_name', 'from_last_name' => 'last_name'])
            ->join(['tu' => 'users'], 'm.to_user_id = tu.id', ['to_email' => 'email', 'to_first_name' => 'first_name', 'to_last_name' => 'last_name'])
            ->order('m.created_at DESC');

        if ($type === 'received') {
            $select->where(['m.to_user_id' => $userId]);
        } else {
            $select->where(['m.from_user_id' => $userId]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $messages = [];
        foreach ($result as $row) {
            $messages[] = $this->hydrateMessage($row);
        }

        return $messages;
    }

    public function getUnreadCount(int $userId): int
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('messages')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')])
            ->where(['to_user_id' => $userId, 'is_read' => 0, 'status' => 'unread']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return (int) $result->current()['count'];
    }

    public function create(Message $message): Message
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('messages');
        
        $data = [
            'from_user_id' => $message->getFromUserId(),
            'to_user_id' => $message->getToUserId(),
            'deal_id' => $message->getDealId(),
            'subject' => $message->getSubject(),
            'content' => $message->getContent(),
            'status' => $message->getStatus(),
            'is_read' => 0,
        ];

        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        $message->setId($this->adapter->getDriver()->getLastGeneratedValue());
        return $message;
    }

    public function markAsRead(int $id, int $userId): bool
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('messages')
            ->set([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s'),
                'status' => 'read',
            ])
            ->where(['id' => $id, 'to_user_id' => $userId]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        return $result->getAffectedRows() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update('messages')
            ->set(['status' => 'deleted'])
            ->where(['id' => $id])
            ->where(function($where) use ($userId) {
                $where->equalTo('from_user_id', $userId)
                    ->or
                    ->equalTo('to_user_id', $userId);
            });

        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        return $result->getAffectedRows() > 0;
    }

    protected function hydrateMessage(array $data): Message
    {
        $message = new Message();
        $message->setId((int) $data['id'])
            ->setFromUserId((int) $data['from_user_id'])
            ->setToUserId((int) $data['to_user_id'])
            ->setDealId($data['deal_id'] ? (int) $data['deal_id'] : null)
            ->setSubject($data['subject'])
            ->setContent($data['content'])
            ->setStatus($data['status'])
            ->setIsRead((bool) $data['is_read'])
            ->setReadAt($data['read_at'] ?? null)
            ->setCreatedAt($data['created_at']);

        if (isset($data['from_email'])) {
            $fromUser = new \User\Model\User();
            $fromUser->setId((int) $data['from_user_id'])
                ->setEmail($data['from_email'])
                ->setFirstName($data['from_first_name'] ?? null)
                ->setLastName($data['from_last_name'] ?? null);
            $message->setFromUser($fromUser);
        }

        if (isset($data['to_email'])) {
            $toUser = new \User\Model\User();
            $toUser->setId((int) $data['to_user_id'])
                ->setEmail($data['to_email'])
                ->setFirstName($data['to_first_name'] ?? null)
                ->setLastName($data['to_last_name'] ?? null);
            $message->setToUser($toUser);
        }

        return $message;
    }
}

