<?php

namespace App\Support\Pagination\Urls;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\FilmQueries;
use App\Support\Pagination\Paginator;

final class FilmUrls
{
    public function __construct(
        private FilmQueries $filmQueries,
        private Paginator $paginator
    ) {
    }
    
    private function getSerialNumberOfItemInList(int $filmId): int
    {
        $film = $this->filmQueries->queryAllRowNumbers()->get()->find($filmId);
        
        return $film ? $film->n : Paginator::PAGINATOR_DEFAULT_SERIAL_NUMBER;
    }
    
    public function getUrlWithPaginationOptionsByRequest(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        return $url.'?'.http_build_query([
            'page' => $paginatorDto->page->value,
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
        ]);
    }
    
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm(string $url, PaginatorDto $dto, int $filmId): string
    {
        $itemNumber = $this->getSerialNumberOfItemInList($filmId);
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getPageOfItem($itemNumber, $dto->perPage->value),
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый фильм попал в список
            'title_filter' => '',
            'description_filter' => '',
            'release_year_filter' => '',
        ]);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        $maxSerialNumber = $this->filmQueries->getFilmsCount($filmFilterDto);
        
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getCurrentPage($maxSerialNumber, $paginatorDto->page->value, $paginatorDto->perPage->value),
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
        ]);
    }
}
