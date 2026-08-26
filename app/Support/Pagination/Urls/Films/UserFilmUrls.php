<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
use App\Queries\Person\UserFilmQueries;
use App\Services\ServiceManagerInterface;

final class UserFilmUrls
{
    private UserFilmQueries $userFilmQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
            private BaseFilmUrls $baseFilmUrls
    ) {
        $this->userFilmQueries = $this->serviceManager->getQueriesOrModifiers(UserFilmQueries::class);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): string
    {
        $maxSerialNumber = $this->userFilmQueries->count($filmFilterDto, $userId);
        
        return $this->baseFilmUrls->getUrlAfterRemovingFilm($url, $paginatorDto, $filmFilterDto, $maxSerialNumber);
    }
}
