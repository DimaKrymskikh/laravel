<?php

namespace App\CommandHandlers\Database\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
use App\Queries\Dvd\FilmsListForPage\FilmsListForPageQueriesInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmsListForPageCommandHandler
{
    public function __construct(
            private FilmsListForPageQueriesInterface $filmQueries,
    ) {
    }
    
    public function handle(PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator
    {
        return $this->filmQueries->get($paginatorDto, $filmFilterDto, $userId);
    }
}
