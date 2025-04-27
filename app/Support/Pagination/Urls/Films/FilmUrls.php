<?php

namespace App\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Support\Pagination\Paginator;

final class FilmUrls
{
    public function __construct(
        private FilmQueriesInterface $filmQueries,
        private BaseFilmUrls $baseFilmUrls
    ) {
    }
    
    /**
     * При редиректе будут переданы текущие параметры пагинации
     * 
     * @param string $url
     * @param PaginatorDto $paginatorDto
     * @param FilmFilterDto $filmFilterDto
     * @return string
     */
    public function getUrlWithPaginationOptionsByRequest(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        return $this->baseFilmUrls->getUrlByRequest($url, $paginatorDto, $filmFilterDto);
    }
    
    /**
     * Задаются параметры пагинации так, чтобы после создания нового или обновлении существующего фильма
     * при редиректе отображалась страница с данным фильмом
     * 
     * @param string $url
     * @param PaginatorDto $dto
     * @param int $filmId
     * @return string
     */
    public function getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm(string $url, PaginatorDto $dto, int $filmId): string
    {
        $film = $this->filmQueries->getRowNumbers()->find($filmId);
        $itemNumber = $film ? $film->n : Paginator::PAGINATOR_DEFAULT_ITEM_NUMBER;
        
        return $this->baseFilmUrls->getUrlAfterCreatingOrUpdatingFilm($url, $dto, $itemNumber);
    }
    
    /**
     * Задаются параметры пагинации так, чтобы после удаления фильма
     * при редиректе отображалась страница, на которой был фильм, 
     * или предыдущая страница, если удалённый фильм был единственным элементом на последней страние
     * 
     * @param string $url
     * @param PaginatorDto $paginatorDto
     * @param FilmFilterDto $filmFilterDto
     * @return string
     */
    public function getUrlWithPaginationOptionsAfterRemovingFilm(string $url, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): string
    {
        // После удаления фильма находим число фильмов с заданными фильтрами
        $maxSerialNumber = $this->filmQueries->count($filmFilterDto);
        
        return $this->baseFilmUrls->getUrlAfterRemovingFilm($url, $paginatorDto, $filmFilterDto, $maxSerialNumber);
    }
}
