<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserCity;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Person\UserCityService;
use PHPUnit\Framework\TestCase;

class UserCityServiceTest extends TestCase
{
    private UserCityModifiersInterface $userCityModifiers;
    private UserCityQueriesInterface $userCityQueries;
    private CityQueriesInterface $cityQueries;
    private UserCityService $userCityService;
    private $userId = 8;
    private $cityId = 29;

    public function test_success_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(false);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->userCityModifiers->expects($this->once())
                ->method('save')
                ->with(new UserCity(), $this->userId, $this->cityId);
        
        $this->userCityService->create($this->userId, $this->cityId);
    }

    public function test_fail_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(true);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityModifiers->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->create($this->userId, $this->cityId);
    }

    public function test_success_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(true);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->userCityModifiers->expects($this->once())
                ->method('delete')
                ->with($this->userId, $this->cityId);
        
        $this->userCityService->delete($this->userId, $this->cityId);
    }

    public function test_fail_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->userId, $this->cityId)
                ->willReturn(false);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityModifiers->expects($this->never())
                ->method('delete');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->delete($this->userId, $this->cityId);
    }
    
    protected function setUp(): void
    {
        $this->userCityModifiers = $this->createMock(UserCityModifiersInterface::class);
        $this->userCityQueries = $this->createMock(UserCityQueriesInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        
        $this->userCityService = new UserCityService($this->userCityModifiers, $this->userCityQueries, $this->cityQueries);
    }
}
