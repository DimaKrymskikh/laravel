<?php

namespace Tests\Unit\Events;

use App\Events\RemoveCityFromWeatherList;
use PHPUnit\Framework\TestCase;

class RemoveCityFromWeatherListTest extends TestCase
{
    private RemoveCityFromWeatherList $removeCityFromWeatherList;
    private int $userId = 51;
    private string $cityName = 'TestCityName';
    
    public function test_city_can_be_add_in_weather_list(): void
    {
        $broadcastWith = $this->removeCityFromWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], "Вы удалили город $this->cityName из списка просмотра погоды");
        
        // Проверка имени канала
        $channelName = $this->removeCityFromWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->removeCityFromWeatherList = new RemoveCityFromWeatherList($this->userId, $this->cityName);
    }
}
