<?php

namespace Tests\Unit\CommandHandlers\Database\Dvd\Films;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Queries\Dvd\Films\FilmsListForPage\FilmsListForPageQueriesInterface;
use PHPUnit\Framework\TestCase;
use Tests\Support\Data\Dto\Database\Dvd\Filters\FilmFilterDtoCase;
use Tests\Support\Data\Dto\Pagination\PaginatorDtoCase;

class FilmsListForPageCommandHandlerTest extends TestCase
{
    use FilmFilterDtoCase, PaginatorDtoCase;
    
    private FilmsListForPageCommandHandler $handler;
    private FilmsListForPageQueriesInterface $queries;
    
    public function test_success_handle(): void
    {
        $filmFilterDto = $this->getBaseCaseFilmFilterDto();
        $paginatorDto = $this->getBaseCasePaginatorDto();
        $userId = 7;
        
        $this->queries->expects($this->once())
                ->method('get')
                ->with($paginatorDto, $filmFilterDto, $userId);
        
        $this->handler->handle($paginatorDto, $filmFilterDto, $userId);
    }
    
    public function test_success_handle_without_user(): void
    {
        $filmFilterDto = $this->getBaseCaseFilmFilterDto();
        $paginatorDto = $this->getBaseCasePaginatorDto();
        
        $this->queries->expects($this->once())
                ->method('get')
                ->with($paginatorDto, $filmFilterDto);
        
        $this->handler->handle($paginatorDto, $filmFilterDto);
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(FilmsListForPageQueriesInterface::class);
        
        $this->handler = new FilmsListForPageCommandHandler($this->queries);
    }
}
