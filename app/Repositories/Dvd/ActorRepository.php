<?php

namespace App\Repositories\Dvd;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use App\Providers\BindingInterfaces\RepositoriesProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ActorRepository implements ActorRepositoryInterface
{
    public function save(Actor $actor, ActorDto $dto): void
    {
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
    }
    
    public function delete(int $actorId): void
    {
        DB::transaction(function () use ($actorId) {
            FilmActor::where('actor_id', $actorId)->delete();
            Actor::find($actorId)->delete();
        });
    }
    
    public function getById(int $actorId): Actor
    {
        return Actor::find($actorId);
    }
    
    public function count(ActorFilterDto $dto): int
    {
        return Actor::filter($dto)->count();
    }
    
    public function getRowNumbers(): Collection
    {
        return Actor::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY first_name, last_name) AS n')
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
    
    public function getList(ActorFilterDto $actorFilterDto): Collection
    {
        return $this->queryActorsList($actorFilterDto)->limit(RepositoriesProvider::DEFAULT_LIMIT)->get();
    }
    
    public function getListForPage(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        $query =  $this->queryActorsList($actorFilterDto);
        
        return $this->paginate($query, $paginatorDto, $actorFilterDto);
    }
    
    private function queryActorsList(ActorFilterDto $dto): Builder
    {
        return Actor::select(
                'id',
                'first_name',
                'last_name'
            )
            ->filter($dto)
            ->orderBy('first_name')
            ->orderBy('last_name');
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
