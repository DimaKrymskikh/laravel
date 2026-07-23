<?php

namespace Tests\Unit\CommandHandlers\Database\Dvd\Actors\CreateActor;

use App\CommandHandlers\Database\Dvd\Actors\CreateActor\CreateActorCommandHandler;
use App\Console\Commands\Dvd\Actors\CreateActor;
use App\Modifiers\Dvd\ActorModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Services\Database\Dvd\ActorService;
use App\Services\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

class CreateActorCommandHandlerTest extends TestCase
{
    private CreateActorCommandHandler $handler;
    private CreateActor $command;
    private ServiceManagerInterface $serviceManager;
    private ActorModifiers $actorModifiers;
    private ActorQueries $actorQueries;
    private ActorService $actorService;

    public function test_success_handle(): void
    {
        $this->command
                ->expects($this->exactly(2))
                ->method('ask')
                // У метода ask два параметра: ask($question, $default = null)
                ->willReturnMap([
                        ['Введите имя актёра?', null, 'Testname'],
                        ['Введите фамилию актёра?', null, 'Testlastname']
                    ]);
        
        $this->actorModifiers->expects($this->once())
                ->method('save');
        
        $this->command
                ->expects($this->never())
                ->method('error');
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_fail_handle(): void
    {
        $this->command
                ->expects($this->exactly(2))
                ->method('ask')
                // У метода ask два параметра: ask($question, $default = null)
                ->willReturnMap([
                        ['Введите имя актёра?', null, ''], // Невалидное имя
                        ['Введите фамилию актёра?', null, 'Testlastname']
                    ]);
        
        $this->actorModifiers->expects($this->never())
                ->method('save');
        
        $this->command
                ->expects($this->once())
                ->method('error');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->command = $this->createMock(CreateActor::class);
        
        $this->actorModifiers = $this->createMock(ActorModifiers::class);
        $this->actorQueries = $this->createMock(ActorQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->actorModifiers, $this->actorQueries);
        
        $this->actorService = new ActorService($this->serviceManager);
        
        $this->handler = new CreateActorCommandHandler($this->actorService);
    }
}
