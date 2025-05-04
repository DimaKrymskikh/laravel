<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\FilmActor;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\Services\Database\Dvd\FilmActorService;
use PHPUnit\Framework\TestCase;

class FilmActorServiceTest extends TestCase
{
    private FilmActorModifiersInterface $filmActorModifiers;
    private ActorQueriesInterface $actorQueries;
    private FilmActorQueriesInterface $filmActorQueries;
    private FilmQueriesInterface $filmQueries;
    private FilmActorService $filmActorService;
    private int $filmId = 5;
    private int $actorId = 18;

    public function test_success_create(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId))
                ->willReturn(false);
        
        $this->filmActorModifiers->expects($this->once())
                ->method('save')
                ->with(new FilmActor(), $this->identicalTo($this->filmId), $this->identicalTo($this->actorId));
        
        $this->filmActorService->create($this->filmId, $this->actorId);
    }

    public function test_fail_create(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId))
                ->willReturn(true);
        
        $this->expectException(DatabaseException::class);
        
        $this->filmActorModifiers->expects($this->never())
                ->method('save');
        
        $this->filmActorService->create($this->filmId, $this->actorId);
    }

    public function test_success_get_actors_list_by_film_id(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('getByFilmId')
                ->with($this->identicalTo($this->filmId));
        
        $this->filmActorService->getActorsListByFilmId($this->filmId);
    }

    public function test_success_delete(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId))
                ->willReturn(true);
        
        $this->filmActorModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId));
        
        $this->filmActorService->delete($this->filmId, $this->actorId);
    }

    public function test_fail_delete(): void
    {
        $this->filmActorQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId))
                ->willReturn(false);
        
        $this->filmActorModifiers->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->filmId), $this->identicalTo($this->actorId));
        
        $this->expectException(DatabaseException::class);
        
        $this->filmActorService->delete($this->filmId, $this->actorId);
    }
    
    protected function setUp(): void
    {
        $this->actorQueries = $this->createMock(ActorQueriesInterface::class);
        $this->filmActorModifiers = $this->createMock(FilmActorModifiersInterface::class);
        $this->filmActorQueries = $this->createMock(FilmActorQueriesInterface::class);
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        
        $this->filmActorService = new FilmActorService($this->actorQueries, $this->filmActorModifiers, $this->filmActorQueries, $this->filmQueries);
    }
}
