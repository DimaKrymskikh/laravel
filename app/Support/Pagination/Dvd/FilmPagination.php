<?php

namespace App\Support\Pagination\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmPagination
{
    /**
     * По строке запроса $query возвращает объект пагинации.
     * 
     * @param Builder $query
     * @param PaginatorDTO $paginatorDto
     * @param FilmFilterDto $filmFilterDto
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $query, PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'title_filter' => $filmFilterDto->title,
                    'description_filter' => $filmFilterDto->description,
                    'release_year_filter' => $filmFilterDto->releaseYear,
                    'language_name_filter' => $filmFilterDto->languageName,
                ]);
    }
}
