<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Modifiers\Dvd\Actors\ActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

final class ActorService
{
    public function __construct(
            private ActorModifiersInterface $actorModifiers,
            private ActorQueriesInterface $actorQueries,
    ) {
    }
    
    public function create(ActorDto $dto): Actor
    {
        $actor = new Actor();
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;

        $this->actorModifiers->save($actor);
        
        return $actor;
    }
    
    public function update(ActorDto $dto, int $actorId): Actor
    {
        $actor = $this->actorQueries->getById($actorId);
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        
        $this->actorModifiers->save($actor);
        
        return $actor;
    }
    
    public function delete(int $actorId): void
    {
        if(!$this->actorQueries->exists($actorId)) {
            throw new DatabaseException(sprintf(ActorQueriesInterface::NOT_RECORD_WITH_ID, $actorId));
        }
        
        $this->actorModifiers->delete($actorId);
    }
    
    public function getAllActorsList(ActorFilterDto $actorFilterDto): Collection
    {
        return $this->actorQueries->getListWithFilter($actorFilterDto);
    }
}
