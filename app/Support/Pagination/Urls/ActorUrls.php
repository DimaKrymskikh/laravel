<?php

namespace App\Support\Pagination\Urls;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Support\Pagination\Paginator;

final class ActorUrls
{
    public function __construct(
        private ActorQueriesInterface $actorQueries,
        private Paginator $paginator
    ) {
    }
    
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingActor(string $url, PaginatorDto $dto, int $actorId): string
    {
        $itemNumber = $this->actorQueries->getNumberInTableByIdWithOrderByFirstNameAndLastName($actorId) ?? Paginator::PAGINATOR_DEFAULT_SERIAL_NUMBER;
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getPageOfItem($itemNumber, $dto->perPage->value),
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый актёр попал в список актёров
            'name' => ''
        ]);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingActor(string $url, PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): string
    {
        $maxItemNumber = $this->actorQueries->count($actorFilterDto);
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getCurrentPage($maxItemNumber, $paginatorDto->page->value, $paginatorDto->perPage->value),
            'number' => $paginatorDto->perPage->value,
            'name' => $actorFilterDto->name
        ]);
    }
}
