<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Actor;
use App\Repositories\Dvd\ActorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class ActorService
{
    public function __construct(
            private ActorRepositoryInterface $actorRepository,
    ) {
    }
    
    public function create(ActorDto $dto): Actor
    {
        $actor = new Actor();
        $this->actorRepository->save($actor, $dto);
        
        return $actor;
    }
    
    public function update(ActorDto $dto, int $actorId): Actor
    {
        $actor = $this->actorRepository->getById($actorId);
        $this->actorRepository->save($actor, $dto);
        
        return $actor;
    }
    
    public function delete(int $actorId): void
    {
        $this->actorRepository->delete($actorId);
    }
    
    public function getAllActorsList(ActorFilterDto $actorFilterDto): Collection
    {
        return $this->actorRepository->getList($actorFilterDto);
    }
    
    public function getActorsListForPage(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        return $this->actorRepository->getListForPage($paginatorDto, $actorFilterDto);
    }
}
