<?php

namespace App\Queries\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Providers\BindingInterfaces\QueriesProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class ActorQueries implements ActorQueriesInterface
{
    public function exists(int $id): bool
    {
        return Actor::where('id', $id)->exists();
    }
    
    public function getById(int $id): Actor
    {
        return Actor::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function count(ActorFilterDto $dto): int
    {
        return Actor::filter($dto)->count();
    }
    
    public function getNumberInTableByIdWithOrderByFirstNameAndLastName(int $id): int|null
    {
        return DB::scalar(<<<SQL
                    SELECT 
                        n
                    FROM (
                        SELECT
                            id,
                            row_number() OVER(ORDER BY first_name, last_name) AS n
                        FROM dvd.actors
                        ORDER BY first_name, last_name 
                    )_
                    WHERE id = :id
                SQL, ['id' => $id]);
    }
    
    public function getList(): Collection
    {
        return $this->getListWithFilter(new ActorFilterDto(''));
    }
    
    public function getListWithFilter(ActorFilterDto $dto): Collection
    {
        return Actor::select(
                    'id',
                    'first_name',
                    'last_name'
                )
                ->filter($dto)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(QueriesProvider::DEFAULT_LIMIT)
                ->get();
    }
}
