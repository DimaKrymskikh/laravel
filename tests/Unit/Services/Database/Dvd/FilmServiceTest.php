<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Services\Database\Dvd\FilmService;
use Illuminate\Database\Eloquent\Collection;
use Tests\Unit\TestCase\DvdTestCase;

class FilmServiceTest extends DvdTestCase
{
    private FilmQueriesInterface $filmQueries;
    private FilmModifiersInterface $filmModifiers;
    private FilmService $filmService;
    private int $filmId = 12;

    public function test_success_create(): void
    {
        $filmDto = $this->getFilmDto();
        
        $this->filmModifiers->expects($this->once())
                ->method('save')
                ->with(new Film(), $filmDto);
        
        $this->assertInstanceOf(Film::class, $this->filmService->create($filmDto));
    }
    
    public function test_success_update(): void
    {
        $film = new Film();
        
        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId))
                ->willReturn($film);
        
        $this->filmModifiers->expects($this->once())
                ->method('saveField')
                ->with($film, 'test_field', 'testValue');
        
        $this->assertInstanceOf(Film::class, $this->filmService->update('test_field', 'testValue', $this->filmId));
    }
    
    public function test_fail_update(): void
    {
        $this->expectException(DatabaseException::class);

        $this->filmQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->filmId))
                ->willThrowException(new DatabaseException(sprintf(FilmQueriesInterface::NOT_RECORD_WITH_ID, $this->filmId)));
        
        $this->filmModifiers->expects($this->never())
                ->method('saveField');
        
        $this->filmService->update('test_field', 'testValue', $this->filmId);
    }
    
    public function test_success_delete(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId))
                ->willReturn(true);
        
        $this->filmModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->filmId));
        
        $this->assertNull($this->filmService->delete($this->filmId));
    }
    
    public function test_fail_delete(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->filmId))
                ->willReturn(false);
        
        $this->filmModifiers->expects($this->never())
                ->method('delete');
        
        $this->expectException(DatabaseException::class);
        
        $this->assertNull($this->filmService->delete($this->filmId));
    }
    
    public function test_success_get_film_card(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('getByIdWithActors')
                ->with($this->identicalTo($this->filmId));
        
        $this->assertInstanceOf(Film::class, $this->filmService->getFilmCard($this->filmId));
    }
    
    public function test_success_get_films_list(): void
    {
        $filmFilterDto = $this->getFilmFilterDto();
        
        $this->filmQueries->expects($this->once())
                ->method('getListWithFilter')
                ->with($filmFilterDto);
        
        $this->assertInstanceOf(Collection::class, $this->filmService->getFilmsList($filmFilterDto));
    }
    
    public function test_success_get_actors_list(): void
    {
        $this->filmQueries->expects($this->once())
                ->method('getByIdWithActors')
                ->with($this->identicalTo($this->filmId));
        
        $this->assertInstanceOf(Film::class, $this->filmService->getActorsList($this->filmId));
    }
    
    protected function setUp(): void
    {
        $this->filmQueries = $this->createMock(FilmQueriesInterface::class);
        $this->filmModifiers = $this->createMock(FilmModifiersInterface::class);
        
        $this->filmService = new FilmService($this->filmQueries, $this->filmModifiers);
    }
}
