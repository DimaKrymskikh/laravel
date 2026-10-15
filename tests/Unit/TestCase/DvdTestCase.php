<?php

namespace Tests\Unit\TestCase;

use App\Contracts\Services\ServiceManagerInterface;
use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Modifiers\Dvd\ActorModifiers;
use App\Modifiers\Dvd\FilmModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmQueries;
use App\ValueObjects\IntValue;
use Tests\Unit\UnitTestCase;

abstract class DvdTestCase extends UnitTestCase
{
    protected ServiceManagerInterface $serviceManager;
    protected ActorModifiers $actorModifiers;
    protected FilmModifiers $filmModifiers;
    protected ActorQueries $actorQueries;
    protected FilmQueries $filmQueries;
    protected int $actorId = 15;
    protected int $filmId = 12;
    
    protected function getFilmDto(): FilmDto
    {
        return new FilmDto('TestTitle', 'TestDescription', IntValue::create('2025'));
    }
    
    protected function getFilmFilterDto(): FilmFilterDto
    {
        return new FilmFilterDto('TestTitle', 'TestDescription', '2025', 'Русский');
    }
    
    protected function create(): void
    {
        $this->filmQueries = $this->createStub(FilmQueries::class);
        $this->filmModifiers = $this->createStub(FilmModifiers::class);
        $this->actorModifiers = $this->createStub(ActorModifiers::class);
        $this->actorQueries = $this->createStub(ActorQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
    }
    
    protected function setUp(): void
    {
        $this->create();
        
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->actorModifiers, $this->filmModifiers, $this->actorQueries, $this->filmQueries);
    }
}
