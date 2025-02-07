<?php

namespace Tests\Unit\Services\Database\Person;

use App\Models\User;
use App\Repositories\Person\UserRepositoryInterface;
use App\Services\Database\Person\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private UserService $userService;
    private User $user;
    private int $userId = 11;

    public function test_success_assign_admin(): void
    {
        $this->userRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->userId))
                ->willReturn($this->user);
        
        $this->userRepository->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', true);
        
        $this->userService->assignAdmin($this->userId);
    }

    public function test_success_deprive_admin(): void
    {
        $this->userRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->userId))
                ->willReturn($this->user);
        
        $this->userRepository->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($this->user), 'is_admin', false);
        
        $this->userService->depriveAdmin($this->userId);
    }
    
    protected function setUp(): void
    {
        $this->user = new User();
        
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        
        $this->userService = new UserService($this->userRepository);
    }
}
