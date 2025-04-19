<?php

namespace App\Support\Pagination\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmPagination
{
    public function paginate(Builder $query, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
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
