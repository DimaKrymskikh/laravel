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
        $this->cityModifiers->expects($this->once())
                ->method('save')
                ->with(new City, $this->name, $this->openWeatherId);
        
        $this->cityService->create($this->name, $this->openWeatherId);
    }

    public function test_success_update_name(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->cityId))
                ->willReturn($city);
        
        $this->cityModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($city), 'name', $this->name);
        
        $this->cityService->update($this->cityId, 'name', $this->name);
    }

    public function test_success_update_timezone_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->cityId))
                ->willReturn($city);
        
        $this->cityModifiers->expects($this->once())
                ->method('saveField')
                ->with($this->identicalTo($city), 'timezone_id', null);
        
        $this->cityService->update($this->cityId, 'timezone_id', '');
    }

    public function test_fail_update(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->expects($this->never())
                ->method('getById');
        
        $this->cityModifiers->expects($this->never())
                ->method('saveField');
        
        $this->cityService->update($this->cityId, 'open_weather_id', '77');
    }

    public function test_success_delete(): void
    {
        $this->cityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(true);
        
        $this->cityModifiers->expects($this->once())
                ->method('delete')
                ->with($this->identicalTo($this->cityId));
        
        $this->cityService->delete($this->cityId);
    }

    public function test_fail_delete(): void
    {
        $this->cityQueries->expects($this->once())
                ->method('exists')
                ->with($this->identicalTo($this->cityId))
                ->willReturn(false);
        
        $this->cityModifiers->expects($this->never())
                ->method('delete')
                ->with($this->identicalTo($this->cityId));
        
        $this->expectException(DatabaseException::class);
        
        $this->cityService->delete($this->cityId);
    }

    public function test_success_get_city_by_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->cityId))
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
                ->with($this->identicalTo($userId));
        
        $this->assertInstanceOf(Collection::class, $this->cityService->getListWithAvailableByUserId($userId));
    }

    public function test_success_find_city_by_open_weather_id(): void
    {
        $city = new City();
        
        $this->cityQueries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->with($this->identicalTo($this->openWeatherId))
                ->willReturn($city);
        
        $this->assertSame($city, $this->cityService->findCityByOpenWeatherId($this->openWeatherId));
    }

    public function test_fail_find_city_by_open_weather_id(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->expects($this->once())
                ->method('getByOpenWeatherId')
                ->with($this->identicalTo($this->openWeatherId))
                ->willReturn(null);
        
        $this->cityService->findCityByOpenWeatherId($this->openWeatherId);
    }
    
    protected function setUp(): void
    {
        $this->cityModifiers = $this->createMock(CityModifiersInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        
        $this->cityService = new CityService($this->cityModifiers, $this->cityQueries);
    }
}
