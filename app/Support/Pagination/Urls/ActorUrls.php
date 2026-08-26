<?php

namespace App\Support\Pagination\Urls;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Pagination\PaginatorDTO;
use App\Queries\Dvd\ActorQueries;
use App\Services\ServiceManagerInterface;

final class ActorUrls
{
    private ActorQueries $actorQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->actorQueries = $this->serviceManager->getQueriesOrModifiers(ActorQueries::class);
    }
    
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingActor(string $url, PaginatorDTO $dto, int $actorId): string
    {
        $itemNumber = $this->actorQueries->getNumberInTableByIdWithOrderByFirstNameAndLastName($actorId) ?? PaginatorDTO::PAGINATOR_DEFAULT_ITEM_NUMBER;
        
        return $url.'?'.http_build_query([
            'page' => $dto->getСurrentPageByItemNumber($itemNumber)->value,
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый актёр попал в список актёров
            'name' => ''
        ]);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingActor(string $url, PaginatorDTO $paginatorDto, ActorFilterDto $actorFilterDto): string
    {
        $maxItemNumber = $this->actorQueries->count($actorFilterDto);
        
        return $url.'?'.http_build_query([
            'page' => $paginatorDto->getСurrentPage($maxItemNumber)->value,
            'number' => $paginatorDto->perPage->value,
            'name' => $actorFilterDto->name
        ]);
    }
}
