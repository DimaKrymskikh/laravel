<?php

namespace Tests\Unit\CommandHandlers\Database\Dvd\Films;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Queries\Dvd\Films\FilmsListForPage\FilmsListForPageQueriesInterface;
use Tests\Unit\TestCase\DvdTestCase;

class FilmsListForPageCommandHandlerTest extends DvdTestCase
{
    private FilmsListForPageCommandHandler $handler;
    private FilmsListForPageQueriesInterface $queries;
    
    public function test_success_handle(): void
    {
        $filmFilterDto = $this->getFilmFilterDto();
        $paginatorDto = $this->getPaginatorDto();
        $userId = 7;
        
        $this->queries->expects($this->once())
                ->method('get')
                ->with($paginatorDto, $filmFilterDto, $userId);
        
        $this->handler->handle($paginatorDto, $filmFilterDto, $userId);
    }
    
    public function test_success_handle_without_user(): void
    {
        $filmFilterDto = $this->getFilmFilterDto();
        $paginatorDto = $this->getPaginatorDto();
        
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
