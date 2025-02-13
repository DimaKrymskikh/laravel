<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Repositories\Person\UserFilmRepositoryInterface;

final class UserFilmUrls
{
    public function __construct(
        private UserFilmRepositoryInterface $userFilmRepository,
        private BaseFilmUrls $baseFilmUrls
    ) {
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): string
    {
        $maxSerialNumber = $this->userFilmRepository->count($filmFilterDto, $userId);
        
        return $this->baseFilmUrls->getUrlAfterRemovingFilm($url, $paginatorDto, $filmFilterDto, $maxSerialNumber);
    }
}
