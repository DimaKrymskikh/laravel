<?php

namespace Tests\Unit\Services\Database\Thesaurus;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Thesaurus\City;
use App\Modifiers\Thesaurus\CityModifiers;
use App\Queries\Thesaurus\CityQueries;
use App\Services\Database\Thesaurus\CityService;
use App\Support\Collections\Thesaurus\CityCollection;
use PHPUnit\Framework\TestCase;

class CityServiceTest extends TestCase
{
    private ServiceManagerInterface $serviceManager;
    private CityModifiers $cityModifiers;
    private CityQueries $cityQueries;
    private CityService $cityService;
    private int $cityId = 3;
    private string $name = 'TestCity';
    private int $openWeatherId = 19;

    public function test_success_getById(): void
    {
        $this->cityQueries->method('find')
                ->willReturn(new City());
        
        $this->assertInstanceOf(City::class, $this->cityService->getCityById($this->cityId));
    }

    public function test_fail_getById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->method('find')
                ->willReturn(null);
        
        $this->cityService->getCityById($this->cityId);
    }

    public function test_success_getByOpenWeatherId(): void
    {
        $this->cityQueries->method('findByOpenWeatherId')
                ->willReturn(new City());
        
        $this->assertInstanceOf(City::class, $this->cityService->getCityByOpenWeatherId($this->openWeatherId));
    }

    public function test_fail_getByOpenWeatherId(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->method('findByOpenWeatherId')
                ->willReturn(null);
        
        $this->cityService->getCityByOpenWeatherId($this->openWeatherId);
    }

    public function test_success_create(): void
    {
        $this->assertInstanceOf(City::class, $this->cityService->create($this->name, $this->openWeatherId));
    }

    public function test_success_update_name(): void
    {
        $this->cityQueries->method('find')
                ->willReturn(new City());
        
        $this->assertInstanceOf(City::class, $this->cityService->update($this->cityId, 'name', $this->name));
    }

    public function test_success_update_timezone_id(): void
    {
        $this->cityQueries->method('find')
                ->willReturn(new City());
        
        $this->assertInstanceOf(City::class, $this->cityService->update($this->cityId, 'timezone_id', ''));
    }

    public function test_fail_update(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityService->update($this->cityId, 'open_weather_id', '77');
    }

    public function test_success_delete(): void
    {
        $this->cityQueries->method('find')
                ->willReturn(new City());
        
        $this->assertNull($this->cityService->delete($this->cityId));
    }

    public function test_fail_delete(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->cityQueries->method('find')
                ->willReturn(null);
        
        $this->cityService->delete($this->cityId);
    }

    public function test_success_getAllCitiesList(): void
    {
        $this->assertInstanceOf(CityCollection::class, $this->cityService->getAllCitiesList());
    }

    public function test_success_getListWithAvailableByUserId(): void
    {
        $userId = 8;
        
        $this->assertInstanceOf(CityCollection::class, $this->cityService->getListWithAvailableByUserId($userId));
    }
    
    public function test_success_getCitiesWithWeatherByUser(): void
    {
        $this->assertInstanceOf(CityCollection::class, $this->cityService->getCitiesWithWeatherByUser(new User()));
    }
    
    protected function setUp(): void
    {
        $this->cityModifiers = $this->createStub(CityModifiers::class);
        $this->cityQueries = $this->createStub(CityQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->cityModifiers, $this->cityQueries);
        
        $this->cityService = new CityService($this->serviceManager);
    }
}
