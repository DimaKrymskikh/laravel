<?php

namespace Tests\Unit\Services\Database\Person;

use App\Models\User;
use App\Modifiers\Person\Users\UserModifiersInterface;
use App\Services\Database\Person\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserModifiersInterface $userModifiers;
    private UserService $userService;
    private User $user;

    public function test_success_assign_admin(): void
    {
        $this->userModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', true);
        
        $this->userService->assignAdmin($this->user);
    }

    public function test_success_deprive_admin(): void
    {
        $this->userModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', false);
        
        $this->userService->depriveAdmin($this->user);
    }
    
    protected function setUp(): void
    {
        $this->user = new User();
        
        $this->userModifiers = $this->createMock(UserModifiersInterface::class);
        
        $this->userService = new UserService($this->userModifiers, $this->userQueries);
    }
}
