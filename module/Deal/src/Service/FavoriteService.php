<?php

namespace Deal\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class FavoriteService
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addFavorite(int $userId, int $dealId): bool
    {
        $sql = new Sql($this->adapter);
        
        // Vérifier si déjà en favori
        $select = $sql->select('favorites')
            ->where(['user_id' => $userId, 'deal_id' => $dealId]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        if ($result->count() > 0) {
            return false; // Déjà en favori
        }

        $insert = $sql->insert('favorites')
            ->values([
                'user_id' => $userId,
                'deal_id' => $dealId,
            ]);

        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return true;
    }

    public function removeFavorite(int $userId, int $dealId): bool
    {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete('favorites')
            ->where(['user_id' => $userId, 'deal_id' => $dealId]);

        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();

        return $result->getAffectedRows() > 0;
    }

    public function isFavorite(int $userId, int $dealId): bool
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('favorites')
            ->where(['user_id' => $userId, 'deal_id' => $dealId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count() > 0;
    }

    public function getUserFavorites(int $userId): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['f' => 'favorites'])
            ->columns(['deal_id'])
            ->where(['f.user_id' => $userId])
            ->order('f.created_at DESC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $dealService = new DealService($this->adapter);
        $deals = [];
        foreach ($result as $row) {
            $deal = $dealService->findById((int) $row['deal_id']);
            if ($deal) {
                $deals[] = $deal;
            }
        }

        return $deals;
    }
}

