<?php

namespace App\Http\Requests\Dvd\Filters;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Http\Requests\PaginatorRequest;

class FilmFilterRequest extends PaginatorRequest
{
    public function getFilmFilterDto(): FilmFilterDto
    {
        return new FilmFilterDto(
                $this->input('title_filter'),
                $this->input('description_filter'),
                $this->input('release_year_filter'),
            );
    }
}
