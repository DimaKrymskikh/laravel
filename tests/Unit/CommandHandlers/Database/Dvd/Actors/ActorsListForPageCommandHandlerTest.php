<?php

namespace Tests\Unit\CommandHandlers\Database\Dvd\Actors;

use App\CommandHandlers\Database\Dvd\Actors\ActorsListForPageCommandHandler;
use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\Actors\ActorsListForPage\ActorsListForPageQueriesInterface;
use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;
use PHPUnit\Framework\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;

class ActorsListForPageCommandHandlerTest extends TestCase
{
    private ActorsListForPageCommandHandler $handler;
    private ActorsListForPageQueriesInterface $queries;

    public function test_success_handle(): void
    {
        $actorFilterDto = new ActorFilterDto('test');
        $paginatorDto = new PaginatorDto(PageValue::create(3), PerPageValue::create(20));
        
        $this->queries->expects($this->once())
                ->method('get')
                ->with($paginatorDto, $actorFilterDto);
        
        $this->assertInstanceOf(LengthAwarePaginator::class, $this->handler->handle($paginatorDto, $actorFilterDto));
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(ActorsListForPageQueriesInterface::class);
        
        $this->handler = new ActorsListForPageCommandHandler($this->queries);
    }
}
