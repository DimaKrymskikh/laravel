<?php

namespace Tests\Unit\Events;

use App\Events\AddCityInWeatherList;
use PHPUnit\Framework\TestCase;

class AddCityInWeatherListTest extends TestCase
{
    private AddCityInWeatherList $addCityInWeatherList;
    private int $userId = 51;
    private string $cityName = 'TestCityName';


    public function test_city_can_be_add_in_weather_list(): void
    {
        $broadcastWith = $this->addCityInWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы добавили город $this->cityName в список просмотра погоды");
        
        // Проверка имени канала
        $channelName = $this->addCityInWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->addCityInWeatherList = new AddCityInWeatherList($this->userId, $this->cityName);
    }
}
