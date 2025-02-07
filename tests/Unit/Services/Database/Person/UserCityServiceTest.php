<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserCity;
use App\Repositories\Person\UserCityRepositoryInterface;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Services\Database\Person\UserCityService;
use PHPUnit\Framework\TestCase;

class UserCityServiceTest extends TestCase
{
    private UserCityRepositoryInterface $userCityRepository;
    private CityRepositoryInterface $cityRepository;
    private UserCityService $userCityService;
    private $userId = 8;
    private $cityId = 29;

    public function test_success_create(): void
    {
        $this->userCityRepository->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(false);
        
        $this->cityRepository->expects($this->never())
                ->method('getById');
        
        $this->userCityRepository->expects($this->once())
                ->method('save')
                ->with(new UserCity(), $this->userId, $this->cityId);
        
        $this->userCityService->create($this->userId, $this->cityId);
    }

    public function test_fail_create(): void
    {
        $this->userCityRepository->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(true);
        
        $this->cityRepository->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityRepository->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->create($this->userId, $this->cityId);
    }

    public function test_success_delete(): void
    {
        $this->userCityRepository->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(true);
        
        $this->cityRepository->expects($this->never())
                ->method('getById');
        
        $this->userCityRepository->expects($this->once())
                ->method('delete')
                ->with($this->userId, $this->cityId);
        
        $this->userCityService->delete($this->userId, $this->cityId);
    }

    public function test_fail_delete(): void
    {
        $this->userCityRepository->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(false);
        
        $this->cityRepository->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityRepository->expects($this->never())
                ->method('delete');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->delete($this->userId, $this->cityId);
    }
    
    protected function setUp(): void
    {
        $this->userCityRepository = $this->createMock(UserCityRepositoryInterface::class);
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        
        $this->userCityService = new UserCityService($this->userCityRepository, $this->cityRepository);
    }
}
