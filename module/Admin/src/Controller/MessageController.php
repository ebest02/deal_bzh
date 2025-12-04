<?php

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Message\Service\MessageService;

class MessageController extends AbstractActionController
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function indexAction(): ViewModel
    {
        $status = $this->params()->fromQuery('status', 'all');
        
        $sql = new \Laminas\Db\Sql\Sql($this->messageService->getAdapter());
        $select = $sql->select(['m' => 'messages'])
            ->join(['fu' => 'users'], 'm.from_user_id = fu.id', ['from_email' => 'email'])
            ->join(['tu' => 'users'], 'm.to_user_id = tu.id', ['to_email' => 'email'])
            ->order('m.created_at DESC');

        if ($status !== 'all') {
            $select->where(['m.status' => $status]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $messages = [];
        foreach ($result as $row) {
            $messages[] = $this->messageService->findById((int) $row['id']);
        }

        return new ViewModel([
            'messages' => $messages,
            'status' => $status,
        ]);
    }

    public function viewAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('id');
        $message = $this->messageService->findById($id);

        if (!$message) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'message' => $message,
        ]);
    }
}

