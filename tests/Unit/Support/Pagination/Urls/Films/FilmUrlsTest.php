<?php

namespace Tests\Unit\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
use App\Queries\Dvd\FilmQueries;
use App\Services\ServiceManagerInterface;
use App\Support\Pagination\Urls\Films\BaseFilmUrls;
use App\Support\Pagination\Urls\Films\FilmUrls;
use Tests\Unit\TestCase\DvdTestCase;

class FilmUrlsTest extends DvdTestCase
{
    private ServiceManagerInterface $serviceManager;
    private FilmQueries $filmQueries;
    private BaseFilmUrls $baseFilmUrls;
    private FilmUrls $filmUrls;
    private int $filmId = 12;
    private FilmFilterDto $filmFilterDto;
    private PaginatorDTO $paginatorDto;

    public function test_getUrlWithPaginationOptionsByRequest(): void
    {
        $url = $this->filmUrls->getUrlWithPaginationOptionsByRequest('test', $this->paginatorDto, $this->filmFilterDto);
        
        $this->assertStringContainsString('page='.$this->paginatorDto->page->value, $url);
        $this->assertStringContainsString('number='.$this->paginatorDto->perPage->value, $url);
        $this->assertStringContainsString('title_filter='.$this->filmFilterDto->title, $url);
        $this->assertStringContainsString('description_filter='.$this->filmFilterDto->description, $url);
        $this->assertStringContainsString('release_year_filter='.$this->filmFilterDto->releaseYear, $url);
        // из-за кириллицы
        $this->assertStringContainsString(http_build_query(['language_name_filter' => $this->filmFilterDto->languageName]), $url);
    }

    public function test_getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm_if_film_exists_in_collection(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('getNumberInTableByIdWithOrderByTitle')
                // Этот фильм на второй странице (проверяем 'page=2')
                ->willReturn(22);
        
        $this->assertStringContainsString(
                'page=2',
                $this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm('test', $this->paginatorDto, $this->filmId)
            );
    }

    public function test_getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm_if_film_is_missing_from_collection(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('getNumberInTableByIdWithOrderByTitle')
                ->willReturn(null);
        
        $this->assertStringContainsString(
                'page='.PaginatorDTO::PAGINATOR_DEFAULT_ITEM_NUMBER,
                $this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm('test', $this->paginatorDto, $this->filmId)
            );
    }

    public function test_getUrlWithPaginationOptionsAfterRemovingFilm_if_film_is_the_only_one_on_the_page(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('count')
                // getPaginatorDto: page=12, number=20 (удалённый фильм был на 12 странице)
                ->willReturn(11*20);
        
        $this->assertStringContainsString(
                'page=11',
                $this->filmUrls->getUrlWithPaginationOptionsAfterRemovingFilm('test', $this->paginatorDto, $this->filmFilterDto)
            );
    }

    public function test_getUrlWithPaginationOptionsAfterRemovingFilm_if_film_is_not_the_only_one_on_the_page(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('count')
                // getPaginatorDto: page=12, number=20 (удалённый фильм был на 12 странице)
                ->willReturn(15*20+7);
        
        $this->assertStringContainsString(
                'page=12',
                $this->filmUrls->getUrlWithPaginationOptionsAfterRemovingFilm('test', $this->paginatorDto, $this->filmFilterDto)
            );
    }
    
    protected function setUp(): void
    {
        $this->filmFilterDto = $this->getFilmFilterDto();
        $this->paginatorDto = $this->getPaginatorDTO();
        
        $this->filmQueries = $this->createMock(FilmQueries::class);
        $this->baseFilmUrls = new BaseFilmUrls();
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->filmQueries);
        
        $this->filmUrls = new FilmUrls($this->serviceManager, $this->baseFilmUrls);
    }
}
