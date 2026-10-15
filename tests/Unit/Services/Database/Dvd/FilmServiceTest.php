<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Services\Database\Dvd\FilmService;
use App\Support\Collections\Dvd\FilmCollection;
use Tests\Unit\TestCase\DvdTestCase;

class FilmServiceTest extends DvdTestCase
{
    private FilmService $filmService;

    public function test_success_create(): void
    {
        $dto = $this->getFilmDto();
        
        $this->assertInstanceOf(Film::class, $this->filmService->create($dto));
    }
    
    public function test_success_update(): void
    {
        $this->filmQueries->method('find')
                ->willReturn(new Film());
        
        $this->assertInstanceOf(Film::class, $this->filmService->update('test_field', 'testValue', $this->filmId));
    }
    
    public function test_fail_update(): void
    {
        $this->expectException(DatabaseException::class);

        $this->filmQueries->method('find')
                ->willReturn(null);
        
        $this->filmModifiers->expects($this->never())
                ->method('save');
        
        $this->filmService->update('test_field', 'testValue', $this->filmId);
    }
    
    public function test_success_delete(): void
    {
        $this->filmQueries->method('exists')
                ->willReturn(true);
        
        $this->assertNull($this->filmService->delete($this->filmId));
    }
    
    public function test_fail_delete(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->filmQueries->method('exists')
                ->willReturn(false);
        
        $this->filmService->delete($this->filmId);
    }
    
    public function test_success_get_film_card(): void
    {
        $this->assertInstanceOf(Film::class, $this->filmService->getFilmCard($this->filmId));
    }
    
    public function test_success_get_films_list(): void
    {
        $filmFilterDto = $this->getFilmFilterDto();
        
        $this->assertInstanceOf(FilmCollection::class, $this->filmService->getFilmsList($filmFilterDto));
    }
    
    public function test_success_get_actors_list(): void
    {
        $this->assertInstanceOf(Film::class, $this->filmService->getActorsList($this->filmId));
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->filmService = new FilmService($this->serviceManager);
    }
}
