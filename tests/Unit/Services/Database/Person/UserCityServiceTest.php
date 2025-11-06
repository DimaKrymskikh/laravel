<?php

namespace Tests\Unit\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\Database\Person\UserCityService;
use PHPUnit\Framework\TestCase;

class UserCityServiceTest extends TestCase
{
    private UserCityModifiersInterface $userCityModifiers;
    private UserCityQueriesInterface $userCityQueries;
    private CityQueriesInterface $cityQueries;
    private UserCityService $userCityService;
    private UserCityDto $dto;
    private $userId = 8;
    private $cityId = 29;

    public function test_success_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->userCityModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->dto));
        
        $this->userCityService->create($this->dto);
    }

    public function test_fail_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityModifiers->expects($this->never())
                ->method('save');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->create($this->dto);
    }

    public function test_success_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->userCityModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->dto));
        
        $this->userCityService->delete($this->dto);
    }

    public function test_fail_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId);
        
        $this->userCityModifiers->expects($this->never())
                ->method('remove');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = new UserCityDto($this->userId, $this->cityId);
        $this->userCityModifiers = $this->createMock(UserCityModifiersInterface::class);
        $this->userCityQueries = $this->createMock(UserCityQueriesInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        
        $this->userCityService = new UserCityService($this->userCityModifiers, $this->userCityQueries, $this->cityQueries);
    }
}
