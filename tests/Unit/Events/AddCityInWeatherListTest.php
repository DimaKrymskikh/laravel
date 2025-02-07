<?php

namespace Tests\Unit\Events;

use App\Events\AddCityInWeatherList;
use App\Models\Thesaurus\City;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Services\Database\Thesaurus\CityService;
use PHPUnit\Framework\TestCase;

class AddCityInWeatherListTest extends TestCase
{
    private AddCityInWeatherList $addCityInWeatherList;
    private CityRepositoryInterface $cityRepository;
    private CityService $cityService;
    private int $cityId = 7;
    private int $userId = 51;
    
    public function test_city_can_be_add_in_weather_list(): void
    {
        $city = new City();
        $city->name = 'TestName';
        
        $this->cityRepository->expects($this->once())
                ->method('getById')
                ->with($this->identicalTo($this->cityId))
                ->willReturn($city);
        
        $broadcastWith = $this->addCityInWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы добавили город $city->name в список просмотра погоды");
        
        // Проверка имени канала
        $channelName = $this->addCityInWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->cityService = new CityService($this->cityRepository);
         
        $this->addCityInWeatherList = new AddCityInWeatherList($this->userId, $this->cityId, $this->cityService);
    }
}
