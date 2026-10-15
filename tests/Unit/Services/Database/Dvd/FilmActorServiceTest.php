<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Modifiers\Dvd\FilmActorModifiers;
use App\Queries\Dvd\FilmActorQueries;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Services\Database\Dvd\FilmActorService;
use App\Support\Collections\Dvd\FilmActorCollection;
use Tests\Unit\TestCase\DvdTestCase;

class FilmActorServiceTest extends DvdTestCase
{
    private FilmActorModifiers $filmActorModifiers;
    private FilmActorQueries $filmActorQueries;
    private FilmActorService $filmActorService;
    private FilmActorDto $dto;

    public function test_success_create(): void
    {
        $this->filmActorQueries->method('exists')
                ->willReturn(false);
        
        $this->assertNull($this->filmActorService->create($this->dto));
    }

    public function test_fail_create(): void
    {
        $this->filmActorQueries->method('exists')
                ->willReturn(true);
        
        $this->expectException(DatabaseException::class);
        
        $this->filmActorService->create($this->dto);
    }

    public function test_success_get_actors_list_by_film_id(): void
    {
        $this->assertInstanceOf(FilmActorCollection::class, $this->filmActorService->getActorsListByFilmId($this->filmId));
    }

    public function test_success_delete(): void
    {
        $this->filmActorQueries->method('exists')
                ->willReturn(true);
        
        $this->assertNull($this->filmActorService->delete($this->dto));
    }

    public function test_fail_delete(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->filmActorQueries->method('exists')
                ->willReturn(false);
        
        $this->filmActorService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = new FilmActorDto($this->filmId, $this->actorId);
        
        $this->create();
        
        $this->filmActorModifiers = $this->createStub(FilmActorModifiers::class);
        $this->filmActorQueries = $this->createStub(FilmActorQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->actorQueries, $this->filmActorModifiers, $this->filmActorQueries, $this->filmQueries);
        
        $this->filmActorService = new FilmActorService($this->serviceManager);
    }
}
