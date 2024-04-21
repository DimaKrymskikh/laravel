<?php

namespace Tests\Feature\Events;

use App\Events\RemoveCityFromWeatherList;
use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class RemoveCityFromWeatherListTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    public function test_city_can_be_remove_from_weather_list(): void
    {
        $this->seedCitiesAndUsers();
        
        $user = $this->getUser('AuthTestLogin');
        
        $cityName = City::find(CitySeeder::ID_TOMSK)->name;
        
        $addCityInWeatherList = new RemoveCityFromWeatherList($user->id, CitySeeder::ID_TOMSK);
        
        $broadcastWith = $addCityInWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], trans('event_messages.remove_city_from_weather_list', ['cityName' => $cityName]));
        
        // Проверка имени канала
        $channelName = $addCityInWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$user->id");
    }
}
