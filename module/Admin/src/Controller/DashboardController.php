<?php

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Service\UserService;
use Deal\Service\DealService;
use Message\Service\MessageService;
use Deal\Service\CategoryService;
use Laminas\Db\Adapter\AdapterInterface;

class DashboardController extends AbstractActionController
{
    protected $userService;
    protected $dealService;
    protected $messageService;
    protected $categoryService;
    protected $adapter;

    public function __construct(
        UserService $userService,
        DealService $dealService,
        MessageService $messageService,
        CategoryService $categoryService,
        AdapterInterface $adapter
    ) {
        $this->userService = $userService;
        $this->dealService = $dealService;
        $this->messageService = $messageService;
        $this->categoryService = $categoryService;
        $this->adapter = $adapter;
    }

    public function indexAction(): ViewModel
    {
        $sql = new \Laminas\Db\Sql\Sql($this->adapter);

        // Statistiques utilisateurs
        $select = $sql->select('users')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $totalUsers = (int) $result->current()['count'];

        // Statistiques annonces
        $select = $sql->select('deals')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $totalDeals = (int) $result->current()['count'];

        $select = $sql->select('deals')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')])
            ->where(['status' => 'published']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $publishedDeals = (int) $result->current()['count'];

        $select = $sql->select('deals')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')])
            ->where(['status' => 'draft']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $draftDeals = (int) $result->current()['count'];

        // Statistiques messages
        $select = $sql->select('messages')
            ->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $totalMessages = (int) $result->current()['count'];

        // Annonces récentes
        $recentDeals = $this->dealService->findAll([], 1, 10);

        return new ViewModel([
            'totalUsers' => $totalUsers,
            'totalDeals' => $totalDeals,
            'publishedDeals' => $publishedDeals,
            'draftDeals' => $draftDeals,
            'totalMessages' => $totalMessages,
            'recentDeals' => $recentDeals,
        ]);
    }
}

