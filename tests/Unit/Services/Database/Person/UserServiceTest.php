<?php

namespace Tests\Unit\Services\Database\Person;

use App\Models\User;
use App\Modifiers\Person\Users\UserModifiersInterface;
use App\Queries\Person\Users\UserQueriesInterface;
use App\Services\Database\Person\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserModifiersInterface $userModifiers;
    private UserQueriesInterface $userQueries;
    private UserService $userService;
    private User $user;
    private int $userId = 11;

    public function test_success_assign_admin(): void
    {
        $this->userQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->userId))
                ->willReturn($this->user);
        
        $this->userModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', true);
        
        $this->userService->assignAdmin($this->userId);
    }

    public function test_success_deprive_admin(): void
    {
        $this->userQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->userId))
                ->willReturn($this->user);
        
        $this->userModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', false);
        
        $this->userService->depriveAdmin($this->userId);
    }
    
    protected function setUp(): void
    {
        $this->user = new User();
        
        $this->userModifiers = $this->createMock(UserModifiersInterface::class);
        $this->userQueries = $this->createMock(UserQueriesInterface::class);
        
        $this->userService = new UserService($this->userModifiers, $this->userQueries);
    }
}
