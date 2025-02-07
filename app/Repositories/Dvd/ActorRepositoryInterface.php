<?php

namespace App\Repositories\Dvd;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Actor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ActorRepositoryInterface
{
    public function save(Actor $actor, ActorDto $dto): void;
    
    public function delete(int $actorId): void;
    
    public function getById(int $actorId): Actor;
    
    public function count(ActorFilterDto $dto): int;
    
    public function getRowNumbers(): Collection;
    
    public function getList(ActorFilterDto $actorFilterDto): Collection;
    
    public function getListForPage(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator;
}
