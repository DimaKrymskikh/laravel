<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Services\Database\Dvd\ActorService;
use App\Support\Collections\Dvd\ActorCollection;
use App\ValueObjects\PersonName;
use Tests\Unit\TestCase\DvdTestCase;

class ActorServiceTest extends DvdTestCase
{
    private ActorService $actorService;
    private ActorDto $actorDto;
    
    public function test_success_create(): void
    {
        $this->assertInstanceOf(Actor::class, $this->actorService->create($this->actorDto));
    }
    
    public function test_success_update(): void
    {
        $this->actorQueries->method('find')
                ->willReturn(new Actor());
        
        $this->assertInstanceOf(Actor::class, $this->actorService->update($this->actorDto, $this->actorId));
    }
    
    public function test_success_delete(): void
    {
        $this->actorQueries->method('exists')
                ->willReturn(true);
        
        $this->assertNull($this->actorService->delete($this->actorId));
    }
    
    public function test_fail_delete(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->actorQueries->method('exists')
                ->willReturn(false);
        
        $this->actorService->delete($this->actorId);
    }
    
    public function test_success_getAllActorsList(): void
    {
        $actorFilterDto = new ActorFilterDto('test');
        
        $this->assertInstanceOf(ActorCollection::class, $this->actorService->getAllActorsList($actorFilterDto));
    }
    
    protected function setUp(): void
    {
        $firstName = PersonName::create('Testfirstname', 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $lastName = PersonName::create('Testlastname', 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $this->actorDto = new ActorDto($firstName, $lastName);
        
        parent::setUp();
        
        $this->actorService = new ActorService($this->serviceManager);
    }
}
