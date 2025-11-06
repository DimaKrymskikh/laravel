<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class CityServiceTest extends TestCase
{
    private CityModifiersInterface $cityModifiers;
    private CityQueriesInterface $cityQueries;
    private CityService $cityService;
    private int $cityId = 3;
    private string $name = 'TestCity';
    private int $openWeatherId = 19;

    public function test_success_create(): void
    {
        $city = new City();
        $city->name = $this->name;
        $city->open_weather_id = $this->openWeatherId;
        
        $this->cityModifiers->expects($this->once())
                ->method('save')
                ->with($city);
        
        $this->assertInstanceOf(City::class, $this->cityService->create($this->name, $this->openWeatherId));
    }

    public function test_success_update_name(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($city);
        
        $this->cityModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($city));
        
        $this->assertInstanceOf(City::class, $this->cityService->update($this->cityId, 'name', $this->name));
    }

    public function test_success_update_timezone_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($city);
        
        $this->cityModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($city));
        
        $this->assertInstanceOf(City::class, $this->cityService->update($this->cityId, 'timezone_id', ''));
    }

    public function test_fail_update(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->cityModifiers->expects($this->never())
                ->method('save');
        
        $this->cityService->update($this->cityId, 'open_weather_id', '77');
    }

    public function test_success_delete(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($city);
        
        $this->cityModifiers->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($city));
        
        $this->cityService->delete($this->cityId);
    }

    public function test_fail_delete(): void
    {
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willThrowException(new DatabaseException(sprintf(CityQueriesInterface::NOT_RECORD_WITH_ID, $this->cityId)));
        
        $this->cityModifiers->expects($this->never())
                ->method('remove');
        
        $this->expectException(DatabaseException::class);
        
        $this->cityService->delete($this->cityId);
    }

    public function test_success_get_city_by_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($city);
        
        $this->assertSame($city, $this->cityService->getCityById($this->cityId));
    }

    public function test_success_get_all_cities_list(): void
    {
        $this->cityQueries->expects($this->once())
                ->method('getList');
        
        $this->assertInstanceOf(Collection::class, $this->cityService->getAllCitiesList());
    }

    public function test_success_get_list_with_available_by_user_id(): void
    {
        $userId = 8;
        
        $this->cityQueries->expects($this->once())
                ->method('getListWithAvailableByUserId')
                ->with($userId);
        
        $this->assertInstanceOf(Collection::class, $this->cityService->getListWithAvailableByUserId($userId));
    }

    public function test_success_find_city_by_open_weather_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->with($this->openWeatherId)
                ->willReturn($city);
        
        $this->assertSame($city, $this->cityService->findCityByOpenWeatherId($this->openWeatherId));
    }

    public function test_fail_find_city_by_open_weather_id(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->with($this->openWeatherId)
                ->willThrowException(new DatabaseException(sprintf(CityQueriesInterface::NOT_RECORD_WITH_ID, $this->cityId)));
        
        $this->cityService->findCityByOpenWeatherId($this->openWeatherId);
    }
    
    protected function setUp(): void
    {
        $this->cityModifiers = $this->createMock(CityModifiersInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        
        $this->cityService = new CityService($this->cityModifiers, $this->cityQueries);
    }
}
