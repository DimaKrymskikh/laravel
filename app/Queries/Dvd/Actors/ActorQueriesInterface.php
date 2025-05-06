<?php

namespace App\Queries\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Queries\SimpleQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface ActorQueriesInterface extends SimpleQueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'dvd.actors' нет записи с id=%d";
    
    public function count(ActorFilterDto $dto): int;
    
    public function getList(ActorFilterDto $actorFilterDto): Collection;
    
    public function getNumberInTableByIdWithOrderByFirstNameAndLastName(int $id): int|null;
}
