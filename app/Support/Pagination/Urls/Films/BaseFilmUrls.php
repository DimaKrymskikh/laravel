<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Support\Pagination\Paginator;

final class BaseFilmUrls
{
    public function __construct(
        private Paginator $paginator
    ) {
    }
    
    public function getUrlByRequest(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        return $url.'?'.http_build_query([
            'page' => $paginatorDto->page->value,
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
        ]);
    }
    
    public function getUrlAfterCreatingOrUpdatingFilm(string $url, PaginatorDto $dto, int $itemNumber): string
    {
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getPageOfItem($itemNumber, $dto->perPage->value),
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый фильм попал в список
            'title_filter' => '',
            'description_filter' => '',
            'release_year_filter' => '',
        ]);
    }
    
    public function getUrlAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $maxSerialNumber): string
    {
        return $url.'?'.http_build_query([
            'page' => $this->paginator->getCurrentPage($maxSerialNumber, $paginatorDto->page->value, $paginatorDto->perPage->value),
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
        ]);
    }
}
