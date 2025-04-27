<?php

namespace Tests\Unit\Support\Pagination\Urls\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Film;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Support\Pagination\Urls\Films\BaseFilmUrls;
use App\Support\Pagination\Urls\Films\FilmUrls;
use App\Support\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Tests\Support\Data\Dto\Database\Dvd\Filters\FilmFilterDtoCase;
use Tests\Support\Data\Dto\Pagination\PaginatorDtoCase;
use PHPUnit\Framework\TestCase;

class FilmUrlsTest extends TestCase
{
    use FilmFilterDtoCase, PaginatorDtoCase;
    
    private FilmQueriesInterface $filmQueries;
    private BaseFilmUrls $baseFilmUrls;
    private FilmUrls $filmUrls;
    private Paginator $paginator;
    private int $filmId = 12;
    private FilmFilterDto $filmFilterDto;
    private PaginatorDto $paginatorDto;

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
        $film1 = new Film();
        $film1->id = 5;
        $film1->n = 1;
        // Этот фильм на второй странице (проверяем 'page=2')
        $film2 = new Film();
        $film2->id = $this->filmId;
        $film2->n = 22;
        
        $collection = new Collection([$film1, $film2]);
        
        $this->filmQueries->expects($this->once())
                ->method('getRowNumbers')
                ->willReturn($collection);
        
        $this->assertStringContainsString(
                'page=2',
                $this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm('test', $this->paginatorDto, $this->filmId)
            );
    }

    public function test_getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm_if_film_is_missing_from_collection(): void
    {
        $collection = new Collection([]);
        
        $this->filmQueries->expects($this->once())
                ->method('getRowNumbers')
                ->willReturn($collection);
        
        $this->assertStringContainsString(
                'page='.Paginator::PAGINATOR_DEFAULT_ITEM_NUMBER,
                $this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm('test', $this->paginatorDto, $this->filmId)
            );
    }

    public function test_getUrlWithPaginationOptionsAfterRemovingFilm_if_film_is_the_only_one_on_the_page(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('count')
                // getBaseCasePaginatorDto: page=12, number=20 (удалённый фильм был на 12 странице)
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
                // getBaseCasePaginatorDto: page=12, number=20 (удалённый фильм был на 12 странице)
                ->willReturn(15*20+7);
        
        $this->assertStringContainsString(
                'page=12',
                $this->filmUrls->getUrlWithPaginationOptionsAfterRemovingFilm('test', $this->paginatorDto, $this->filmFilterDto)
            );
    }
    
    protected function setUp(): void
    {
        $this->filmFilterDto = $this->getBaseCaseFilmFilterDto();
        $this->paginatorDto = $this->getBaseCasePaginatorDto();
        
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->paginator = new Paginator();
        $this->baseFilmUrls = new BaseFilmUrls($this->paginator);
        
        $this->filmUrls = new FilmUrls($this->filmQueries, $this->baseFilmUrls);
    }
}
