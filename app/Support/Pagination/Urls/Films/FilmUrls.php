<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Repositories\Dvd\FilmRepositoryInterface;

final class FilmUrls
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository,
        private BaseFilmUrls $baseFilmUrls
    ) {
    }
    
    public function getUrlWithPaginationOptionsByRequest(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        return $this->baseFilmUrls->getUrlByRequest($url, $paginatorDto, $filmFilterDto);
    }
    
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm(string $url, PaginatorDto $dto, int $filmId): string
    {
        $film = $this->filmRepository->getRowNumbers()->find($filmId);
        $itemNumber = $film ? $film->n : Paginator::PAGINATOR_DEFAULT_SERIAL_NUMBER;
        
        return $this->baseFilmUrls->getUrlAfterCreatingOrUpdatingFilm($url, $dto, $itemNumber);
    }
    
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        $maxSerialNumber = $this->filmRepository->count($filmFilterDto);
        
        return $this->baseFilmUrls->getUrlAfterRemovingFilm($url, $paginatorDto, $filmFilterDto, $maxSerialNumber);
    }
}
