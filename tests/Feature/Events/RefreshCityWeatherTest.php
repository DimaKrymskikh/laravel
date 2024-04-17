<?php

namespace Tests\Feature\Events;

use App\Events\RefreshCityWeather;
use App\Models\Thesaurus\City;
use App\Services\Database\Thesaurus\TimezoneService;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class RefreshCityWeatherTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    /**
     * Не знаю, как проверить время с временным поясом
     */
    public function test_weather_can_be_refresh(): void
    {
        $this->seedCitiesAndUsersWithWeather();
        
        $user = $this->getUser('AuthTestLogin');
        $city = City::find(CitySeeder::ID_NOVOSIBIRSK);
        
        $refreshCityWeather = new RefreshCityWeather($city->id, $user->id, new TimezoneService());
        
        $broadcastWith = $refreshCityWeather->broadcastWith();
        // Проверка отправляемых данных
        $weather = $broadcastWith['weather']->toArray();
        $this->assertEquals($weather['city_id'], CitySeeder::ID_NOVOSIBIRSK);
        $this->assertEquals($weather['weather_description'], 'Ясно');
        $this->assertEquals($weather['main_temp'], '22.91');
        $this->assertEquals($weather['main_feels_like'], '20');
        $this->assertEquals($weather['main_pressure'], 1020);
        $this->assertEquals($weather['main_humidity'], 85);
        $this->assertEquals($weather['wind_speed'], '5');
        $this->assertEquals($weather['wind_deg'], 210);
        $this->assertEquals($weather['clouds_all'], 0);
        
        $this->assertEquals($broadcastWith['cityId'], CitySeeder::ID_NOVOSIBIRSK);
        
        // Проверка имени канала
        $channelName = $refreshCityWeather->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$user->id");
    }
}
