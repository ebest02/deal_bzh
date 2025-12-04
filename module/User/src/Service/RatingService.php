<?php

namespace User\Service;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class RatingService
{
    protected $adapter;
    protected $userService;

    public function __construct(AdapterInterface $adapter, UserService $userService)
    {
        $this->adapter = $adapter;
        $this->userService = $userService;
    }

    public function createRating(int $raterId, int $ratedUserId, int $dealId, int $score, ?string $comment = null): bool
    {
        $sql = new Sql($this->adapter);
        
        // Vérifier si une notation existe déjà pour cette transaction
        $select = $sql->select('ratings')
            ->where([
                'rater_id' => $raterId,
                'rated_user_id' => $ratedUserId,
                'deal_id' => $dealId,
            ]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        if ($result->count() > 0) {
            // Mettre à jour la notation existante
            $update = $sql->update('ratings')
                ->set([
                    'score' => $score,
                    'comment' => $comment,
                ])
                ->where(['id' => $result->current()['id']]);
            
            $statement = $sql->prepareStatementForSqlObject($update);
            $statement->execute();
        } else {
            // Créer une nouvelle notation
            $insert = $sql->insert('ratings')
                ->values([
                    'rater_id' => $raterId,
                    'rated_user_id' => $ratedUserId,
                    'deal_id' => $dealId,
                    'score' => $score,
                    'comment' => $comment,
                ]);
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
        }

        // Mettre à jour la note moyenne de l'utilisateur
        $this->updateUserRating($ratedUserId);

        return true;
    }

    protected function updateUserRating(int $userId): void
    {
        $sql = new Sql($this->adapter);
        
        // Calculer la moyenne et le nombre de notes
        $select = $sql->select('ratings')
            ->columns([
                'avg_score' => new \Laminas\Db\Sql\Expression('AVG(score)'),
                'count' => new \Laminas\Db\Sql\Expression('COUNT(*)'),
            ])
            ->where(['rated_user_id' => $userId]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $data = $result->current();
        
        $avgScore = round((float) $data['avg_score'], 2);
        $count = (int) $data['count'];
        
        // Mettre à jour l'utilisateur
        $update = $sql->update('users')
            ->set([
                'rating' => $avgScore,
                'rating_count' => $count,
            ])
            ->where(['id' => $userId]);
        
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    public function getUserRatings(int $userId): array
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(['r' => 'ratings'])
            ->join(['u' => 'users'], 'r.rater_id = u.id', ['rater_email' => 'email', 'rater_first_name' => 'first_name', 'rater_last_name' => 'last_name'])
            ->join(['d' => 'deals'], 'r.deal_id = d.id', ['deal_title' => 'title'])
            ->where(['r.rated_user_id' => $userId])
            ->order('r.created_at DESC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $ratings = [];
        foreach ($result as $row) {
            $ratings[] = [
                'id' => (int) $row['id'],
                'score' => (int) $row['score'],
                'comment' => $row['comment'],
                'rater' => [
                    'id' => (int) $row['rater_id'],
                    'email' => $row['rater_email'],
                    'first_name' => $row['rater_first_name'],
                    'last_name' => $row['rater_last_name'],
                ],
                'deal' => [
                    'id' => (int) $row['deal_id'],
                    'title' => $row['deal_title'],
                ],
                'created_at' => $row['created_at'],
            ];
        }

        return $ratings;
    }
}

