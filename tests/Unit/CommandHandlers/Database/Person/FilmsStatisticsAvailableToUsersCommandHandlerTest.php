<?php

namespace Tests\Unit\CommandHandlers\Database\Person;

use App\CommandHandlers\Database\Person\FilmsStatisticsAvailableToUsersCommandHandler;
use App\Console\Commands\Person\FilmsStatisticsAvailableToUsers;
use App\Queries\Person\Users\UserQueriesInterface;
use PHPUnit\Framework\TestCase;

class FilmsStatisticsAvailableToUsersCommandHandlerTest extends TestCase
{
    private FilmsStatisticsAvailableToUsersCommandHandler $handler;
    private FilmsStatisticsAvailableToUsers $command;
    private UserQueriesInterface $queries;

    public function test_success_handle_writeFile(): void
    {
        $this->queries->expects($this->once())
                ->method('getArray');
        
        $this->command->expects($this->once())
                ->method('option')
                ->willReturn(true);
        
        $this->command->expects($this->once())
                ->method('writeFile');
        
        $this->command->expects($this->never())
                ->method('writeConsole');
        
        $this->assertNull($this->handler->handle($this->command));
    }

    public function test_success_handle_writeConsole(): void
    {
        $this->queries->expects($this->once())
                ->method('getArray');
        
        $this->command->expects($this->once())
                ->method('option')
                ->willReturn(false);
        
        $this->command->expects($this->never())
                ->method('writeFile');
        
        $this->command->expects($this->once())
                ->method('writeConsole');
        
        $this->assertNull($this->handler->handle($this->command));
    }
    
    protected function setUp(): void
    {
        $this->command = $this->createMock(FilmsStatisticsAvailableToUsers::class);
        $this->queries = $this->createMock(UserQueriesInterface::class);
        
        $this->handler = new FilmsStatisticsAvailableToUsersCommandHandler($this->queries);
    }
}
