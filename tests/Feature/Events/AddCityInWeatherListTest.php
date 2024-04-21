<?php

namespace Tests\Feature\Events;

use App\Events\AddCityInWeatherList;
use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class AddCityInWeatherListTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    public function test_city_can_be_add_in_weather_list(): void
    {
        $this->seedCitiesAndUsers();
        
        $user = $this->getUser('AuthTestLogin');
        
        $cityName = City::find(CitySeeder::ID_TOMSK)->name;
        
        $addCityInWeatherList = new AddCityInWeatherList($user->id, CitySeeder::ID_TOMSK);
        
        $broadcastWith = $addCityInWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], trans('event_messages.add_city_in_weather_list', ['cityName' => $cityName]));
        
        // Проверка имени канала
        $channelName = $addCityInWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$user->id");
    }
}
