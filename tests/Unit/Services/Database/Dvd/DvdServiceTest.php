<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Models\Dvd\Film;
use App\Services\Database\Dvd\DvdService;
use Tests\Unit\TestCase\DvdTestCase;

class DvdServiceTest extends DvdTestCase
{
    private DvdService $dvdService;

    public function test_success_getActorById(): void
    {
        $this->actorQueries->method('find')
                ->willReturn(new Actor());
        
        $this->assertInstanceOf(Actor::class, $this->dvdService->getActorById($this->actorId));
    }
    
    public function test_fail_getActorById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->actorQueries->method('find')
                ->willReturn(null);
        
        $this->dvdService->getActorById($this->actorId);
    }
    
    public function test_success_getFilmById(): void
    {
        $this->filmQueries->method('find')
                ->willReturn(new Film());
        
        $this->assertInstanceOf(Film::class, $this->dvdService->getFilmById($this->filmId));
    }

    public function test_fail_getFilmById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->filmQueries->method('find')
                ->willReturn(null);
        
        $this->dvdService->getFilmById($this->filmId);
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->dvdService = new class($this->serviceManager) extends DvdService {
        };
    }
}
