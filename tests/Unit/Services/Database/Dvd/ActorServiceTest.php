<?php

namespace Tests\Unit\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Modifiers\Dvd\Actors\ActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Services\Database\Dvd\ActorService;
use App\ValueObjects\PersonName;
use PHPUnit\Framework\TestCase;

class ActorServiceTest extends TestCase
{
    private ActorModifiersInterface $actorModifiers;
    private ActorQueriesInterface $actorQueries;
    private ActorService $actorService;
    private int $actorId = 12;
    
    public function test_success_create(): void
    {
        $firstName = PersonName::create('Testfirstname', 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $lastName = PersonName::create('Testlastname', 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $actorDto = new ActorDto($firstName, $lastName);
        
        $this->actorModifiers->expects($this->once())
                ->method('save');
        
        $this->actorService->create($actorDto);
    }
    
    public function test_success_update(): void
    {
        $firstName = PersonName::create('Testfirstname', 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $lastName = PersonName::create('Testlastname', 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.');
        $actorDto = new ActorDto($firstName, $lastName);
        $actor = new Actor();
        
        $this->actorQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->actorId))
                ->willReturn($actor);
        
        $this->actorModifiers->expects($this->once())
                ->method('save')
                ->with($actor, $actorDto);
        
        $this->actorService->update($actorDto, $this->actorId);
    }
    
    public function test_success_delete(): void
    {
        $this->actorQueries->expects($this->once())
                ->method('exists')
                ->with($this->actorId)
                ->willReturn(true);
        
        $this->actorModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->actorId));
        
        $this->actorService->delete($this->actorId);
    }
    
    public function test_fail_delete(): void
    {
        $this->actorQueries->expects($this->once())
                ->method('exists')
                ->with($this->actorId)
                ->willReturn(false);
        
        $this->actorModifiers->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->actorId));
        
        $this->expectException(DatabaseException::class);
        
        $this->actorService->delete($this->actorId);
    }
    
    public function test_success_getAllActorsList(): void
    {
        $actorFilterDto = new ActorFilterDto('test');
        
        $this->actorQueries->expects($this->once())
                ->method('getList')
                ->with($actorFilterDto);
        
        $this->actorService->getAllActorsList($actorFilterDto);
    }
    
    protected function setUp(): void
    {
        $this->actorModifiers = $this->createMock(ActorModifiersInterface::class);
        $this->actorQueries = $this->createMock(ActorQueriesInterface::class);
        
        $this->actorService = new ActorService($this->actorModifiers, $this->actorQueries);
    }
}
