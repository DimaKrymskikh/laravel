<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Modifiers\Dvd\FilmActorModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Dvd\FilmActorQueries;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Services\Database\Dvd\FilmActorService;
use App\Services\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

class FilmActorServiceTest extends TestCase
{
    private ServiceManagerInterface $serviceManager;
    private FilmActorModifiers $filmActorModifiers;
    private ActorQueries $actorQueries;
    private FilmActorQueries $filmActorQueries;
    private FilmQueries $filmQueries;
    private FilmActorService $filmActorService;
    private FilmActorDto $dto;
    private int $filmId = 5;
    private int $actorId = 18;

    public function test_success_create(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->dto)
                ->willReturn(false);
        
        $this->filmActorModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->dto));
        
        $this->filmActorService->create($this->dto);
    }

    public function test_fail_create(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->dto)
                ->willReturn(true);
        
        $this->expectException(DatabaseException::class);
        
        $this->filmActorModifiers->expects($this->never())
                ->method('save');
        
        $this->filmActorService->create($this->dto);
    }

    public function test_success_get_actors_list_by_film_id(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('getByFilmId')
                ->with($this->filmId);
        
        $this->filmActorService->getActorsListByFilmId($this->filmId);
    }

    public function test_success_delete(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->dto)
                ->willReturn(true);
        
        $this->filmActorModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->dto));
        
        $this->filmActorService->delete($this->dto);
    }

    public function test_fail_delete(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->dto)
                ->willReturn(false);
        
        $this->filmActorModifiers->expects($this->never())
                ->method('remove');
        
        $this->expectException(DatabaseException::class);
        
        $this->filmActorService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = new FilmActorDto($this->filmId, $this->actorId);
        
        $this->actorQueries = $this->createMock(ActorQueries::class);
        $this->filmActorModifiers = $this->createMock(FilmActorModifiers::class);
        $this->filmActorQueries = $this->createMock(FilmActorQueries::class);
        $this->filmQueries = $this->createMock(FilmQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->actorQueries, $this->filmActorModifiers, $this->filmActorQueries, $this->filmQueries);
        
        $this->filmActorService = new FilmActorService($this->serviceManager);
    }
}
