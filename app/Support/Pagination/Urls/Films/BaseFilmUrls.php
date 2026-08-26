<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;

final class BaseFilmUrls
{
    public function getUrlByRequest(string $url, PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        return $url.'?'.http_build_query([
            'page' => $paginatorDto->page->value,
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
            'language_name_filter' => $filmFilterDto->languageName,
        ]);
    }
    
    public function getUrlAfterCreatingOrUpdatingFilm(string $url, PaginatorDTO $dto, int $itemNumber): string
    {
        return $url.'?'.http_build_query([
            'page' => $dto->getСurrentPageByItemNumber($itemNumber)->value,
            'number' => $dto->perPage->value,
            // Нужно сбросить фильтр поиска, чтобы новый или изменённый фильм попал в список
            'title_filter' => '',
            'description_filter' => '',
            'release_year_filter' => '',
            'language_name_filter' => '',
        ]);
    }
    
    public function getUrlAfterRemovingFilm(string $url, PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto, int $maxSerialNumber): string
    {
        return $url.'?'.http_build_query([
            'page' => $paginatorDto->getСurrentPage($maxSerialNumber)->value,
            'number' => $paginatorDto->perPage->value,
            'title_filter' => $filmFilterDto->title,
            'description_filter' => $filmFilterDto->description,
            'release_year_filter' => $filmFilterDto->releaseYear,
            'language_name_filter' => $filmFilterDto->languageName,
        ]);
    }
}
