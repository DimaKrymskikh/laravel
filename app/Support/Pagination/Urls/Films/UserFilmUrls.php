<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;

final class UserFilmUrls
{
    public function __construct(
        private UserFilmQueriesInterface $userFilmQueries,
        private BaseFilmUrls $baseFilmUrls
    ) {
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): string
    {
        $maxSerialNumber = $this->userFilmQueries->count($filmFilterDto, $userId);
        
        return $this->baseFilmUrls->getUrlAfterRemovingFilm($url, $paginatorDto, $filmFilterDto, $maxSerialNumber);
    }
}
