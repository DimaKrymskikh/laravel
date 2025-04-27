<?php

namespace App\CommandHandlers\Database\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\Films\FilmsListForPage\FilmsListForPageQueriesInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmsListForPageCommandHandler
{
    public function __construct(
            private FilmsListForPageQueriesInterface $filmQueries,
    ) {
    }
    
    public function handle(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator
    {
        return $this->filmQueries->get($paginatorDto, $filmFilterDto, $userId);
    }
}
