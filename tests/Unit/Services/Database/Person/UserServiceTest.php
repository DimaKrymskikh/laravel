<?php

namespace Tests\Unit\Services\Database\Person;

use App\Models\User;
use App\Modifiers\Person\UserModifiers;
use App\Services\Database\Person\Dto\RegisterDto;
use App\Services\Database\Person\UserService;
use App\Services\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private ServiceManagerInterface $serviceManager;
    private UserModifiers $userModifiers;
    private UserService $userService;
    private User $user;

    public function test_success_create(): void
    {
        $dto = new RegisterDto('TestLogin', 'TestEmail', 'TestPassword');
        
        $this->userModifiers->expects($this->once())
                ->method('create')
                ->with($this->user, $this->identicalTo($dto));
        
        $this->assertInstanceOf(User::class, $this->userService->create($dto));
    }

    public function test_success_assign_admin(): void
    {
        $this->user->is_admin = false;
        
        $this->userModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->user));
        
        $this->assertInstanceOf(User::class, $this->userService->assignAdmin($this->user));
        $this->assertTrue($this->user->is_admin);
    }

    public function test_success_deprive_admin(): void
    {
        $this->user->is_admin = true;
        
        $this->userModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->user));
        
        $this->assertInstanceOf(User::class, $this->userService->depriveAdmin($this->user));
        $this->assertFalse($this->user->is_admin);
    }

    public function test_success_remove(): void
    {
        $this->userModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->user));
        
        $this->assertNull($this->userService->remove($this->user));
    }
    
    protected function setUp(): void
    {
        $this->user = new User();
        
        $this->userModifiers = $this->createMock(UserModifiers::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->userModifiers);
        
        $this->userService = new UserService($this->serviceManager);
    }
}
