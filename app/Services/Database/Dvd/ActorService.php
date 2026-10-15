<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Support\Collections\Dvd\ActorCollection;

final class ActorService extends DvdService
{
    /**
     * Создаёт новую запись в таблице 'dvd.actors'.
     * 
     * @param ActorDto $dto
     * @return Actor
     */
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
        $actor = $this->getActorById($actorId);
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        
        $this->actorModifiers->save($actor);
        
        return $actor;
    }
    
    public function delete(int $actorId): void
    {
        if(!$this->actorQueries->exists($actorId)) {
            throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_DVD_ACTORS, $actorId));
        }
        
        $this->actorModifiers->delete($actorId);
    }
    
    public function getAllActorsList(ActorFilterDto $actorFilterDto): ActorCollection
    {
        return $this->actorQueries->getListWithFilter($actorFilterDto);
    }
}
