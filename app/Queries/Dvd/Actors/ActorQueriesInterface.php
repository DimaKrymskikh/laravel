<?php

namespace App\Queries\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Queries\QueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface ActorQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'dvd.actors' нет записи с id=%d";
    
    public function count(ActorFilterDto $dto): int;
    
    public function getListWithFilter(ActorFilterDto $dto): Collection;
    
    public function getNumberInTableByIdWithOrderByFirstNameAndLastName(int $id): int|null;
}
