<?php

namespace Tests\Unit\CommandHandlers\Database\Dvd\Actors\CreateActor;

use App\CommandHandlers\Database\Dvd\Actors\CreateActor\CreateActorCommandHandler;
use App\Console\Commands\Dvd\Actors\CreateActor;
use App\Services\Database\Dvd\ActorService;
use Tests\Unit\TestCase\DvdTestCase;

class CreateActorCommandHandlerTest extends DvdTestCase
{
    private CreateActorCommandHandler $handler;
    private CreateActor $command;
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
        
        $this->command
                ->expects($this->once())
                ->method('error');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->command = $this->createMock(CreateActor::class);
        
        $this->actorService = new ActorService($this->serviceManager);
        
        $this->handler = new CreateActorCommandHandler($this->actorService);
    }
}
