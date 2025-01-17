<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\ActorQueries;
use App\Services\Database\BaseDatabaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ActorService extends BaseDatabaseService
{
    public function __construct(
        private ActorQueries $actorQueries,
    ) {
    }
    
    public function create(ActorDto $dto): Actor
    {
        $actor = new Actor();
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
        
        return $actor;
    }
    
    public function update(ActorDto $dto, int $actor_id): Actor
    {
        $actor = Actor::find($actor_id);
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
        
        return $actor;
    }
    
    public function delete(int $actorId): void
    {
        DB::transaction(function () use ($actorId) {
            FilmActor::where('actor_id', $actorId)->delete();
            Actor::find($actorId)->delete();
        });
    }
    
    public function getAllActorsList(ActorFilterDto $actorFilterDto): Collection
    {
        return $this->actorQueries->queryActorsList($actorFilterDto)->limit(BaseDatabaseService::DEFAULT_LIMIT)->get();
    }
    
    public function getActorsListForPage(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        $query =  $this->actorQueries->queryActorsList($actorFilterDto);
        
        return $this->paginate($query, $paginatorDto, $actorFilterDto);
    }
    
    private function paginate(Builder $query, PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'name' => $actorFilterDto->name
                ]);
    }
}
