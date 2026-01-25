<?php

namespace Tests\Unit\Services\Database\Person;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Models\Thesaurus\City;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\Database\Person\UserCityService;
use Illuminate\Contracts\Events\Dispatcher;

class UserCityServiceTest extends UserTestCase
{
    private UserCityModifiersInterface $userCityModifiers;
    private UserCityQueriesInterface $userCityQueries;
    private CityQueriesInterface $cityQueries;
    private UserCityService $userCityService;
    private Dispatcher $dispatcher;
    private UserCityDto $dto;
    private City $city;

    public function test_success_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->userCityModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($this->dto));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with(new AddCityInWeatherList($this->dto->userId, $this->city->name));
        
        $this->userCityService->create($this->dto);
    }

    public function test_fail_create(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->userCityModifiers->expects($this->never())
                ->method('save');
        
        $this->dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->create($this->dto);
    }

    public function test_success_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(true);
        
        $this->userCityModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($this->dto));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with(new RemoveCityFromWeatherList($this->dto->userId, $this->city->name));
        
        $this->userCityService->delete($this->dto);
    }

    public function test_fail_delete(): void
    {
        $this->userCityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->dto))
                ->willReturn(false);
        
        $this->userCityModifiers->expects($this->never())
                ->method('remove');
        
        $this->dispatcher->expects($this->never())
                ->method('dispatch');
        
        $this->expectException(DatabaseException::class);
        
        $this->userCityService->delete($this->dto);
    }
    
    protected function setUp(): void
    {
        $this->dto = $this->getUserCityDto();
        $this->city = $this->factoryCity();
        
        $this->userCityModifiers = $this->createMock(UserCityModifiersInterface::class);
        $this->userCityQueries = $this->createMock(UserCityQueriesInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        
        $this->userCityService = new UserCityService($this->userCityModifiers, $this->userCityQueries, $this->cityQueries, $this->dispatcher);
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->dto->cityId)
                ->willReturn($this->city);
    }
}
