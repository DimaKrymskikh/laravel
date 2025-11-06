<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Services\Database\Dvd\FilmActorService;
use PHPUnit\Framework\TestCase;

class FilmActorServiceTest extends TestCase
{
    private FilmActorModifiersInterface $filmActorModifiers;
    private ActorQueriesInterface $actorQueries;
    private FilmActorQueriesInterface $filmActorQueries;
    private FilmQueriesInterface $filmQueries;
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
        $this->actorQueries = $this->createMock(ActorQueriesInterface::class);
        $this->filmActorModifiers = $this->createMock(FilmActorModifiersInterface::class);
        $this->filmActorQueries = $this->createMock(FilmActorQueriesInterface::class);
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        
        $this->filmActorService = new FilmActorService($this->actorQueries, $this->filmActorModifiers, $this->filmActorQueries, $this->filmQueries);
    }
}
