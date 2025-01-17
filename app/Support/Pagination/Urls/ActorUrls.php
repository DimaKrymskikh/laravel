<?php

namespace App\Support\Pagination\Urls;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\ActorQueries;
use App\Support\Pagination\Paginator;

final class ActorUrls
{
    public function __construct(
        private ActorQueries $actorQueries,
        private Paginator $paginator
    ) {
    }
    
    /**
     * Возвращает номер актёра в списке/таблице актёров с фильтрами и с сортировкой
     * 
     * @param int $actorId - id актёра
     * @return int
     */
    private function getSerialNumberOfItemInList(int $actorId): int
    {
        $actor = $this->actorQueries->queryAllRowNumbers()->get()->find($actorId);
        
        return $actor ? $actor->n : Paginator::PAGINATOR_DEFAULT_SERIAL_NUMBER;
    }
    
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingActor(string $url, PaginatorDto $dto, int $actorId): string
    {
        $itemNumber = $this->getSerialNumberOfItemInList($actorId);
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getPageOfItem($itemNumber, $dto->perPage->value),
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый актёр попал в список актёров
            'name' => ''
        ]);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingActor(string $url, PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): string
    {
        $maxItemNumber = $this->actorQueries->getActorsCount($actorFilterDto);
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getCurrentPage($maxItemNumber, $paginatorDto->page->value, $paginatorDto->perPage->value),
            'number' => $paginatorDto->perPage->value,
            'name' => $actorFilterDto->name
        ]);
    }
}
